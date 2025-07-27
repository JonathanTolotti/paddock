<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
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
