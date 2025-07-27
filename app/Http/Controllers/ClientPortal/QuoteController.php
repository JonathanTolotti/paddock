<?php

namespace App\Http\Controllers\ClientPortal;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Services\WorkOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;

class QuoteController extends Controller
{
    public function show(WorkOrder $workOrder): Response
    {
        $workOrder->load(['client', 'vehicle', 'parts', 'services', 'statusHistories']);

        $statusHistories = $workOrder->statusHistories->map(function ($history) {
            return [
                'id' => $history->id,
                'status' => $history->status->value,
                'status_label' => $history->status->label(),
                'notes' => $history->notes,
                'created_at' => $history->created_at,
            ];
        });

        $approveUrl = URL::temporarySignedRoute(
            'quote.approve',
            now()->addDays((int) env('TIME_URL_SIGNATURE')),
            ['workOrder' => $workOrder->uuid]
        );

        $rejectUrl = URL::temporarySignedRoute(
            'quote.reject',
            now()->addDays((int) env('TIME_URL_SIGNATURE')),
            ['workOrder' => $workOrder->uuid]
        );

        return Inertia::render('ClientPortal/Quote', [
            'workOrder' => $workOrder,
            'statusHistories' => $statusHistories,
            'approveUrl' => $approveUrl,
            'rejectUrl' => $rejectUrl,
        ]);
    }

    public function approve(WorkOrder $workOrder, WorkOrderService $service): RedirectResponse
    {
        // Reutiliza a nossa lógica de aprovação (que já dá baixa no estoque)
        $service->approve($workOrder);

        // Redireciona de volta para a mesma página com uma mensagem de sucesso
        return redirect()->back()->with('success', 'Orçamento aprovado com sucesso! Entraremos em contato em breve.');
    }

    public function reject(WorkOrder $workOrder, WorkOrderService $service): RedirectResponse
    {
        $service->cancel($workOrder);

        return redirect()->back()->with('success', 'Orçamento reprovado.');
    }
}
