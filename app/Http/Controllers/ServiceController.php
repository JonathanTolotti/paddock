<?php

namespace App\Http\Controllers;

use App\Http\Requests\Services\StoreServiceRequest;
use App\Http\Requests\Services\UpdateServiceRequest;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Services/Index', [
            'services' => Service::orderBy('name')->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Services/Create');
    }

    public function store(StoreServiceRequest $request, ServiceService $serviceService): RedirectResponse
    {
        $serviceService->create($request->validated());
        return redirect()->route('services.index')->with('success', 'Serviço cadastrado com sucesso.');
    }

    public function edit(Service $service): Response
    {
        return Inertia::render('Services/Edit', [
            'service' => $service,
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service, ServiceService $serviceService): RedirectResponse
    {
        $serviceService->update($service, $request->validated());
        return redirect()->route('services.index')->with('success', 'Serviço atualizado com sucesso.');
    }

    public function destroy(Service $service, ServiceService $serviceService): RedirectResponse
    {
        $serviceService->delete($service);
        return redirect()->route('services.index')->with('success', 'Serviço excluído com sucesso.');
    }
    public function search(Request $request): JsonResponse
    {
        $term = $request->query('q', '');
        $services = Service::where('name', 'LIKE', "%{$term}%")
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json($services);
    }
}
