<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Almacen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    /**
     * GET /api/almacen
     *
     * Lista el inventario actual por ubicación.
     *
     * Filtros opcionales:
     *   ?location_id=3          → todo lo que hay en esa ubicación
     *   ?product_id=5           → dónde está ese equipo
     *   ?consumable_id=8        → en qué ubicaciones hay stock de ese consumible
     *   ?kind=equipment|consumable → filtrar solo equipos o solo consumibles
     */
    public function index(Request $request): JsonResponse
    {
        $query = Almacen::query()
            ->with(['product', 'consumable', 'location']);

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->integer('location_id'));
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->integer('product_id'));
        }

        if ($request->filled('consumable_id')) {
            $query->where('consumable_id', $request->integer('consumable_id'));
        }

        if ($request->input('kind') === 'equipment') {
            $query->whereNotNull('product_id');
        } elseif ($request->input('kind') === 'consumable') {
            $query->whereNotNull('consumable_id');
        }

        $results = $query
            ->orderBy('location_id')
            ->paginate($request->integer('per_page', 50));

        return response()->json($results);
    }

    /**
     * GET /api/almacen/{almacen}
     *
     * Detalle de una entrada específica del almacén.
     */
    public function show(Almacen $almacen): JsonResponse
    {
        return response()->json(
            $almacen->load(['product', 'consumable', 'location'])
        );
    }
}
