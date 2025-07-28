<?php

namespace App\Http\Controllers;

use App\Http\Requests\Parts\StorePartRequest;
use App\Http\Requests\Parts\UpdatePartRequest;
use App\Models\Part;
use App\Services\PartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Routing\Controller as BaseController;

class PartController extends BaseController
{
    public function __construct()
    {
        $this->middleware('permission:manage_catalog');
    }
    public function index(): Response
    {
        $parts = Part::orderBy('name')->paginate(15);

        return Inertia::render('Parts/Index', [
            'parts' => $parts,
        ]);
    }
    public function search(Request $request): JsonResponse
    {
        $term = $request->query('q', '');
        $parts = Part::where('name', 'LIKE', "%{$term}%")
            ->orWhere('sku', 'LIKE', "%{$term}%")
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json($parts);
    }

    public function create(): Response
    {
        return Inertia::render('Parts/Create');
    }

    public function store(StorePartRequest $request, PartService $partService): RedirectResponse
    {
        $partService->create($request->validated());
        return redirect()->route('parts.index')->with('success', 'Peça cadastrada com sucesso.');
    }

    public function edit(Part $part): Response
    {
        return Inertia::render('Parts/Edit', [
            'part' => $part,
        ]);
    }

    public function update(UpdatePartRequest $request, Part $part, PartService $partService): RedirectResponse
    {
        $partService->update($part, $request->validated());
        return redirect()->route('parts.index')->with('success', 'Peça atualizada com sucesso.');
    }

    public function destroy(Part $part, PartService $partService): RedirectResponse
    {
        $partService->delete($part);

        return redirect()->route('parts.index')
            ->with('success', 'Peça excluída com sucesso.');
    }
}
