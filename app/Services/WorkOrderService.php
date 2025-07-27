<?php

namespace App\Services;

use App\Enums\WorkOrderStatus;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;

class WorkOrderService
{
    public function create(array $data): WorkOrder
    {
        return WorkOrder::create($data);
    }

    public function awaitingApproval(WorkOrder $workOrder): void
    {
        if ($workOrder->status !== WorkOrderStatus::BUDGET) {
            return;
        }
        $workOrder->status = WorkOrderStatus::AWAITING_APPROVAL;
        $workOrder->save();
    }

    public function approve(WorkOrder $workOrder): void
    {
        if ($workOrder->status->value !== WorkOrderStatus::BUDGET->value) {
            return;
        }

        DB::transaction(function () use ($workOrder) {
            foreach ($workOrder->parts as $part) {
                $quantityInOrder = $part->pivot->quantity;

                $part->quantity_reserved = max(0, $part->quantity_reserved - $quantityInOrder);
                $part->quantity -= $quantityInOrder;

                if ($part->quantity < 0 || $part->quantity_reserved < 0) {
                    throw new \Exception("Erro de estoque para a peça {$part->name}. Operação cancelada.");
                }

                $part->save();
            }

            $workOrder->status = WorkOrderStatus::APPROVED;
            $workOrder->save();
        });
    }

    public function startProgress(WorkOrder $workOrder): void
    {
        if ($workOrder->status !== WorkOrderStatus::APPROVED) {
            return;
        }
        $workOrder->status = WorkOrderStatus::IN_PROGRESS;
        $workOrder->save();
    }

    public function finish(WorkOrder $workOrder): void
    {
        if ($workOrder->status !== WorkOrderStatus::IN_PROGRESS) {
            return;
        }
        $workOrder->status = WorkOrderStatus::FINISHED;
        $workOrder->finished_at = now(); // Registra a data de conclusão
        $workOrder->save();
    }

    /**
     * @throws \Throwable
     */
    public function cancel(WorkOrder $workOrder): void
    {
        // Não pode cancelar uma OS já finalizada
        if ($workOrder->status === WorkOrderStatus::FINISHED) {
            return;
        }

        $originalStatus = $workOrder->status;

        DB::transaction(function () use ($workOrder, $originalStatus) {
            if ($originalStatus === WorkOrderStatus::BUDGET || $originalStatus === WorkOrderStatus::APPROVED || $originalStatus === WorkOrderStatus::IN_PROGRESS) {

                foreach ($workOrder->parts as $part) {
                    $quantityInOrder = $part->pivot->quantity;
                    $partToUpdate = $part->fresh();

                    if ($originalStatus === WorkOrderStatus::APPROVED || $originalStatus === WorkOrderStatus::IN_PROGRESS) {
                        $partToUpdate->quantity += $quantityInOrder;
                    }

                    $partToUpdate->quantity_reserved = max(0, $partToUpdate->quantity_reserved - $quantityInOrder);
                    $partToUpdate->save();
                }
            }

            $workOrder->status = WorkOrderStatus::CANCELED;
            $workOrder->save();
        });
    }

}
