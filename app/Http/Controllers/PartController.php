<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PartController extends Controller
{
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
}
