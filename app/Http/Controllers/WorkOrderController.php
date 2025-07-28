<?php

namespace App\Http\Controllers;

use App\Enums\WorkOrderStatus;
use App\Http\Requests\WorkOrders\StoreWorkOrderRequest;
use App\Http\Requests\WorkOrders\UpdateStatusRequest;
use App\Models\Client;
use App\Models\Part;
use App\Models\Service;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\WorkOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Routing\Controller as BaseController;


class WorkOrderController extends BaseController
{

    public function __construct()
    {
        $this->middleware('permission:view_work_orders', ['only' => ['index']]);
        $this->middleware('permission:create_work_orders', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_work_orders', ['only' => ['edit', 'update', 'updateStatus', 'addService', 'removeService', 'addPart', 'removePart']]);
        $this->middleware('permission:delete_work_orders', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = WorkOrder::with(['client', 'vehicle']);

        $user = Auth::user();

        if ($user->hasRole('Mecânico')) {
            $query->where('mechanic_id', $user->id);
        }

        // Aplica o filtro de busca, se existir
        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->whereHas('client', function ($q) use ($term) {
                    $q->where('name', 'LIKE', "%{$term}%");
                })->orWhereHas('vehicle', function ($q) use ($term) {
                    $q->where('plate', 'LIKE', "%{$term}%");
                });
            });
        }

        $workOrders = $query->latest()->paginate(10)
            ->withQueryString();

        return Inertia::render('WorkOrders/Index', [
            'workOrders' => $workOrders,
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        // Passamos todos os clientes para o formulário
        $clients = Client::orderBy('name')->limit(10)->get(['id', 'uuid', 'name', 'document_number']);

        return Inertia::render('WorkOrders/Create', [
            'clients' => $clients,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkOrderRequest $request, WorkOrderService $workOrderService): RedirectResponse
    {
        $workOrder = $workOrderService->create($request->validated());

        return redirect()->route('work-orders.index')
            ->with('success', 'Ordem de Serviço criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkOrder $workOrder): Response
    {
        $workOrder->load(['client', 'vehicle', 'parts', 'services']);

        $mechanics = User::role('Mecânico')->get(['id', 'name']);

        return Inertia::render('WorkOrders/Edit', [
            'workOrder' => $workOrder,
            'mechanics' => $mechanics,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function addService(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        if ($workOrder->status !== WorkOrderStatus::BUDGET) {
            abort(403, 'Não é possível adicionar itens a uma OS que não está em orçamento.');
        }

        $request->validate([
            'service_id' => ['required', 'exists:services,id'],
        ]);

        $service = Service::find($request->input('service_id'));

        $workOrder->services()->attach($service->id, ['price' => $service->price]);

        $totalServicesPrice = DB::table('service_work_order')
            ->where('work_order_id', $workOrder->id)
            ->sum('price');

        $totalPartsPrice = $workOrder->parts()->get()->sum(function ($part) {
            return $part->pivot->quantity * $part->pivot->sale_price;
        });

        $workOrder->total_services_price = $totalServicesPrice;
        $workOrder->total_parts_price = $totalPartsPrice;
        $workOrder->total_price = $totalServicesPrice + $totalPartsPrice;
        $workOrder->save();

        return back()->with('success', 'Serviço adicionado com sucesso.');
    }

    public function addPart(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        if ($workOrder->status !== WorkOrderStatus::BUDGET) {
            abort(403, 'Não é possível adicionar itens a uma OS que não está em orçamento.');
        }

        $request->validate([
            'part_id' => ['required', 'exists:parts,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $part = Part::find($request->input('part_id'));
        $quantity = $request->input('quantity');

        $workOrder->parts()->syncWithoutDetaching([
            $part->id => [
                'quantity' => $quantity,
                'sale_price' => $part->sale_price,
            ],
        ]);

        $totalServicesPrice = $workOrder->services()->sum('service_work_order.price');
        $totalPartsPrice = $workOrder->parts()->get()->sum(function ($part) {
            return $part->pivot->quantity * $part->pivot->sale_price;
        });

        $workOrder->total_services_price = $totalServicesPrice;
        $workOrder->total_parts_price = $totalPartsPrice;
        $workOrder->total_price = $totalServicesPrice + $totalPartsPrice;
        $workOrder->save();

        return back()->with('success', 'Peça adicionada com sucesso.');
    }

    public function removeService(WorkOrder $workOrder, Service $service): RedirectResponse
    {
        if ($workOrder->status !== WorkOrderStatus::BUDGET) {
            abort(403, 'Não é possível adicionar itens a uma OS que não está em orçamento.');
        }

        $workOrder->services()->detach($service->id);

        $totalServicesPrice = $workOrder->services()->sum('service_work_order.price');
        $totalPartsPrice = $workOrder->parts()->get()->sum(function ($part) {
            return $part->pivot->quantity * $part->pivot->sale_price;
        });

        $workOrder->total_services_price = $totalServicesPrice;
        $workOrder->total_parts_price = $totalPartsPrice;
        $workOrder->total_price = $totalServicesPrice + $totalPartsPrice;
        $workOrder->save();

        return back()->with('success', 'Serviço removido com sucesso.');
    }

    public function removePart(WorkOrder $workOrder, Part $part): RedirectResponse
    {
        if ($workOrder->status !== WorkOrderStatus::BUDGET) {
            abort(403, 'Não é possível adicionar itens a uma OS que não está em orçamento.');
        }

        $workOrder->parts()->detach($part->id);

        $totalServicesPrice = $workOrder->services()->sum('service_work_order.price');
        $totalPartsPrice = $workOrder->parts()->get()->sum(function ($part) {
            return $part->pivot->quantity * $part->pivot->sale_price;
        });

        $workOrder->total_services_price = $totalServicesPrice;
        $workOrder->total_parts_price = $totalPartsPrice;
        $workOrder->total_price = $totalServicesPrice + $totalPartsPrice;
        $workOrder->save();

        return back()->with('success', 'Peça removida com sucesso.');
    }

    public function updateStatus(UpdateStatusRequest $request, WorkOrder $workOrder, WorkOrderService $service): RedirectResponse
    {
        $newStatus = WorkOrderStatus::from($request->input('status'));

        match ($newStatus) {
            WorkOrderStatus::AWAITING_APPROVAL => $service->awaitingApproval($workOrder),
            WorkOrderStatus::APPROVED => $service->approve($workOrder),
            WorkOrderStatus::IN_PROGRESS => $service->startProgress($workOrder),
            WorkOrderStatus::FINISHED => $service->finish($workOrder),
            WorkOrderStatus::CANCELED => $service->cancel($workOrder),
            default => null,
        };

        $redirect = redirect()->back()->with('success', 'Status da OS atualizado com sucesso.');

        if ($newStatus === WorkOrderStatus::AWAITING_APPROVAL) {
            $url = URL::temporarySignedRoute(
                'quote.view',
                now()->addDays(7),
                ['workOrder' => $workOrder->uuid]
            );

            $redirect->with('quote_url', $url);
        }

        return $redirect;
    }

    public function assignMechanic(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $request->validate([
            'mechanic_id' => ['required', 'exists:users,id'],
        ]);

        $workOrder->update([
            'mechanic_id' => $request->input('mechanic_id'),
        ]);

        return back()->with('success', 'Mecânico atribuído com sucesso.');
    }
}
