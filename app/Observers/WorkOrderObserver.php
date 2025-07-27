<?php

namespace App\Observers;

use App\Enums\WorkOrderStatus;
use App\Models\WorkOrder;
use App\Models\WorkOrderStatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class WorkOrderObserver
{

    public function creating(WorkOrder $workOrder): void
    {
        if (Auth::check()) {
            $workOrder->user_id = Auth::id();
        }

        if (empty($workOrder->status)) {
            $workOrder->status = WorkOrderStatus::BUDGET;
        }
    }
    /**
     * Handle the WorkOrder "created" event.
     */
    public function created(WorkOrder $workOrder): void
    {
        $url = URL::temporarySignedRoute(
            'quote.view',
            now()->addDays((int) env('TIME_URL_SIGNATURE')),
            ['workOrder' => $workOrder->uuid]
        );

        $workOrder->updateQuietly(['signed_url' => $url]);

        WorkOrderStatusHistory::query()->create([
            'work_order_id' => $workOrder->id,
            'status' => $workOrder->status,
            'user_id' => Auth::id(),
            'notes' => 'Ordem de Serviço criada.',
        ]);
    }

    /**
     * Handle the WorkOrder "updated" event.
     */
    public function updated(WorkOrder $workOrder): void
    {
        if ($workOrder->isDirty('status')) {
            $newStatus = $workOrder->getDirty()['status'];
            $originalStatus = $workOrder->getOriginal('status');

            $newStatusValue = $newStatus instanceof WorkOrderStatus ? $newStatus->value : $newStatus;
            $originalStatusValue = $originalStatus instanceof WorkOrderStatus ? $originalStatus->value : $originalStatus;

            WorkOrderStatusHistory::create([
                'work_order_id' => $workOrder->id,
                'status' => $newStatusValue,
                'user_id' => Auth::id(),
                'notes' => 'Status alterado de ' . $originalStatusValue . '.',
            ]);
        }
    }

    /**
     * Handle the WorkOrder "deleted" event.
     */
    public function deleted(WorkOrder $workOrder): void
    {
        //
    }

    /**
     * Handle the WorkOrder "restored" event.
     */
    public function restored(WorkOrder $workOrder): void
    {
        //
    }

    /**
     * Handle the WorkOrder "force deleted" event.
     */
    public function forceDeleted(WorkOrder $workOrder): void
    {
        //
    }
}
