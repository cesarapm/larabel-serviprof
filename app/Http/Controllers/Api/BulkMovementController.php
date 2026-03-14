<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\InventoryStatus;
use App\Models\Consumable;
use App\Models\ConsumableMovement;
use App\Models\EquipmentMovement;
use App\Models\Product;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Recibe un arreglo de movimientos (equipos o consumibles) y los procesa
 * en una sola transacción. Ideal para captura tipo tabla/grilla desde el frontend.
 */
class BulkMovementController extends Controller
{
    /**
     * POST /api/bulk-movements/equipment
     *
     * Body:
     * {
     *   "type": "movimiento_interno",          // tipo global (se puede sobrescribir por fila)
     *   "personnel_id": 2,                    // responsable global
     *   "movement_date": "2026-03-13",         // fecha global (opcional)
     *   "notes": "Traslado general",           // nota global (opcional)
     *   "rows": [
     *     { "product_id": 5, "location_id": 3, "notes": "fila específica" },
     *     { "product_id": 7, "location_id": 4 },
     *     ...
     *   ]
     * }
     */
    public function equipment(Request $request): JsonResponse
    {
        $globalData = $request->validate([
            'type'         => ['required', Rule::in(['entrada', 'salida', 'renta', 'venta', 'mantenimiento', 'movimiento_interno', 'retorno'])],
            'personnel_id' => ['required', 'exists:personnel,id'],
            'client_id'    => ['nullable', 'exists:clients,id'],
            'date_out'     => ['nullable', 'date'],
            'date_return'  => ['nullable', 'date', 'after_or_equal:date_out'],
            'notes'        => ['nullable', 'string'],
            'rows'         => ['required', 'array', 'min:1', 'max:100'],
        ]);

        $request->validate([
            'rows.*.product_id'  => ['required', 'exists:products,id'],
            'rows.*.location_id' => ['nullable', 'exists:locations,id'],
            'rows.*.notes'       => ['nullable', 'string'],
        ]);

        // Validar disponibilidad para movimiento_interno fila a fila
        if ($globalData['type'] === 'movimiento_interno') {
            $errors = [];
            foreach ($request->input('rows') as $index => $row) {
                $product = Product::find($row['product_id']);
                if (! $product) {
                    continue;
                }
                if ($product->inventory_status !== InventoryStatus::DISPONIBLE) {
                    $errors["rows.{$index}.product_id"] = [
                        "El equipo #{$product->id} ({$product->brand} {$product->model}) no está disponible (estado: {$product->inventory_status->value}).",
                    ];
                }
                if (empty($row['location_id'])) {
                    $errors["rows.{$index}.location_id"] = [
                        "La ubicación destino es obligatoria en movimiento_interno (fila " . ($index + 1) . ").",
                    ];
                }
            }

            if (! empty($errors)) {
                throw ValidationException::withMessages($errors);
            }
        }

        $created = DB::transaction(function () use ($globalData, $request): array {
            $movements = [];

            foreach ($request->input('rows') as $row) {
                $movements[] = EquipmentMovement::create([
                    'product_id'            => $row['product_id'],
                    'type'                  => $globalData['type'],
                    'personnel_id'          => $globalData['personnel_id'],
                    'client_id'             => $globalData['client_id'] ?? null,
                    'location_id'           => $row['location_id'] ?? ($globalData['location_id'] ?? null),
                    'date_out'              => $globalData['date_out'] ?? null,
                    'date_return'           => $globalData['date_return'] ?? null,
                    'notes'                 => $row['notes'] ?? ($globalData['notes'] ?? null),
                    'current_counter_bw'    => $row['current_counter_bw'] ?? null,
                    'current_counter_color' => $row['current_counter_color'] ?? null,
                    'counter_read_at'       => $row['counter_read_at'] ?? null,
                ]);
            }

            return $movements;
        });

        return response()->json([
            'message'  => count($created) . ' movimiento(s) de equipo registrado(s).',
            'total'    => count($created),
            'movements' => collect($created)->map->load(['product', 'client', 'location', 'personnel']),
        ], 201);
    }

    /**
     * POST /api/bulk-movements/consumables
     *
     * Body:
     * {
     *   "type": "movimiento_interno",         // tipo global
     *   "personnel_id": 2,                   // responsable global
     *   "movement_date": "2026-03-13",        // fecha
     *   "notes": "Reubicación general",       // nota global (opcional)
     *   "rows": [
     *     { "consumable_id": 8, "quantity": 10, "location_id": 4, "notes": "..." },
     *     { "consumable_id": 12, "quantity": 5, "location_id": 4 },
     *     ...
     *   ]
     * }
     */
    public function consumables(Request $request): JsonResponse
    {


        $globalData = $request->validate([
            'type'          => ['required', Rule::in(['entrada', 'salida', 'ajuste', 'movimiento_interno', 'vendido'])],
            'personnel_id'  => ['required', 'exists:personnel,id'],
            'client_id'     => ['nullable', 'exists:clients,id'],
            'movement_date' => ['required', 'date'],
            'notes'         => ['nullable', 'string'],
            'rows'          => ['required', 'array', 'min:1', 'max:100'],
        ]);

        $request->validate([
            'rows.*.consumable_id'     => ['required', 'exists:consumables,id'],
            'rows.*.quantity'          => ['required', 'integer', 'min:1'],
            'rows.*.location_id'       => ['nullable', 'exists:locations,id'],
            'rows.*.from_location_id'  => ['nullable', 'exists:locations,id'],
            'rows.*.notes'             => ['nullable', 'string'],
        ]);

        // Validar stock fila a fila para salidas y movimiento_interno
        if (in_array($globalData['type'], ['salida', 'movimiento_interno', 'vendido'])) {
            $errors = [];
            foreach ($request->input('rows') as $index => $row) {
                $consumable = Consumable::find($row['consumable_id']);
                if (! $consumable) {
                    continue;
                }
                if ($row['quantity'] > $consumable->stock_quantity) {
                    $errors["rows.{$index}.quantity"] = [
                        "La cantidad ({$row['quantity']}) supera el stock disponible de '{$consumable->name}' ({$consumable->stock_quantity}).",
                    ];
                }
            }

            if (! empty($errors)) {
                throw ValidationException::withMessages($errors);
            }
        }

        $created = DB::transaction(function () use ($globalData, $request): array {
            $movements = [];

            foreach ($request->input('rows') as $row) {
                $movements[] = ConsumableMovement::create([
                    'consumable_id'    => $row['consumable_id'],
                    'type'             => $globalData['type'],
                    'personnel_id'     => $globalData['personnel_id'],
                    'client_id'        => $globalData['client_id'] ?? null,
                    'location_id'      => $row['location_id'] ?? ($globalData['location_id'] ?? null),
                    'from_location_id' => $row['from_location_id'] ?? null,
                    'quantity'         => $row['quantity'],
                    'movement_date'    => $globalData['movement_date'],
                    'notes'            => $row['notes'] ?? ($globalData['notes'] ?? null),
                ]);
            }

            return $movements;
        });

        return response()->json([
            'message'   => count($created) . ' movimiento(s) de consumible registrado(s).',
            'total'     => count($created),
            'movements' => collect($created)->map->load(['consumable', 'consumable.almacen.location', 'client', 'location', 'fromLocation', 'personnel']),
        ], 201);
    }

    /**
     * POST /api/bulk-movements
     *
     * Endpoint mixto: permite capturar equipos Y consumibles en la misma grilla.
     * Cada fila indica su "kind": "equipment" o "consumable".
     *
     * Reglas de ubicación destino:
     *   - movimiento_interno → location_id OBLIGATORIO por fila
     *   - mantenimiento      → location_id recomendado (taller destino)
     *   - renta              → client_id global; location_id opcional (cliente)
     *   - salida / venta     → SIN ubicación (el material sale del inventario)
     *   - entrada            → SIN ubicación destino (solo ingresa)
     *
     * Body:
     * {
     *   "type": "movimiento_interno",
     *   "personnel_id": 2,
     *   "date": "2026-03-13",
     *   "client_id": null,
     *   "date_return": null,
     *   "notes": "Traslado general",
     *   "rows": [
     *     { "kind": "equipment",   "product_id": 5,  "location_id": 3 },
     *     { "kind": "equipment",   "product_id": 8,  "location_id": 4 },
     *     { "kind": "consumable",  "consumable_id": 12, "quantity": 5,  "location_id": 4 },
     *     { "kind": "consumable",  "consumable_id": 15, "quantity": 20, "location_id": 4 }
     *   ]
     * }
     */
    public function mixed(Request $request): JsonResponse
    {
        // Tipos exclusivos por kind
        $equipmentOnlyTypes  = ['renta', 'venta', 'mantenimiento'];
        $consumableOnlyTypes = ['ajuste'];
        // Tipos que requieren ubicación destino
        $locationRequiredTypes = ['movimiento_interno'];

        $globalData = $request->validate([
            'type'        => ['required', Rule::in(['entrada', 'salida', 'renta', 'venta', 'mantenimiento', 'movimiento_interno', 'ajuste'])],
            'personnel_id'=> ['required', 'exists:personnel,id'],
            'client_id'   => ['nullable', 'exists:clients,id'],
            'date'        => ['required', 'date'],
            'date_return' => ['nullable', 'date', 'after_or_equal:date'],
            'notes'       => ['nullable', 'string'],
            'rows'        => ['required', 'array', 'min:1', 'max:100'],
        ]);

        $request->validate([
            'rows.*.kind'          => ['required', Rule::in(['equipment', 'consumable'])],
            'rows.*.product_id'    => ['nullable', 'exists:products,id'],
            'rows.*.consumable_id' => ['nullable', 'exists:consumables,id'],
            'rows.*.quantity'      => ['nullable', 'integer', 'min:1'],
            'rows.*.location_id'   => ['nullable', 'exists:locations,id'],
            'rows.*.notes'         => ['nullable', 'string'],
        ]);

        $type   = $globalData['type'];
        $errors = [];

        foreach ($request->input('rows') as $index => $row) {
            $kind    = $row['kind'];
            $rowNum  = $index + 1;

            // Tipo incompatible con el kind de la fila
            if ($kind === 'consumable' && in_array($type, $equipmentOnlyTypes)) {
                $errors["rows.{$index}.kind"][] = "El tipo '{$type}' no aplica para consumibles (fila {$rowNum}).";
                continue;
            }
            if ($kind === 'equipment' && in_array($type, $consumableOnlyTypes)) {
                $errors["rows.{$index}.kind"][] = "El tipo '{$type}' no aplica para equipos (fila {$rowNum}).";
                continue;
            }

            // ID obligatorio según kind
            if ($kind === 'equipment' && empty($row['product_id'])) {
                $errors["rows.{$index}.product_id"][] = "El campo product_id es obligatorio para filas de equipo (fila {$rowNum}).";
            }
            if ($kind === 'consumable' && empty($row['consumable_id'])) {
                $errors["rows.{$index}.consumable_id"][] = "El campo consumable_id es obligatorio para filas de consumible (fila {$rowNum}).";
            }
            if ($kind === 'consumable' && empty($row['quantity'])) {
                $errors["rows.{$index}.quantity"][] = "La cantidad es obligatoria para filas de consumible (fila {$rowNum}).";
            }

            // Ubicación obligatoria para movimiento_interno
            if (in_array($type, $locationRequiredTypes) && empty($row['location_id'])) {
                $errors["rows.{$index}.location_id"][] = "La ubicación destino es obligatoria para '{$type}' (fila {$rowNum}).";
            }

            // Equipo: debe estar disponible para movimiento_interno
            if ($kind === 'equipment' && $type === 'movimiento_interno' && ! empty($row['product_id'])) {
                $product = Product::find($row['product_id']);
                if ($product && $product->inventory_status !== InventoryStatus::DISPONIBLE) {
                    $errors["rows.{$index}.product_id"][] =
                        "El equipo #{$product->id} ({$product->brand} {$product->model}) no está disponible (estado: {$product->inventory_status->value}).";
                }
            }

            // Consumible: cantidad no puede superar el stock en salida/movimiento_interno
            if ($kind === 'consumable' && in_array($type, ['salida', 'movimiento_interno'])
                && ! empty($row['consumable_id']) && ! empty($row['quantity'])) {
                $consumable = Consumable::find($row['consumable_id']);
                if ($consumable && $row['quantity'] > $consumable->stock_quantity) {
                    $errors["rows.{$index}.quantity"][] =
                        "La cantidad ({$row['quantity']}) supera el stock disponible de '{$consumable->name}' ({$consumable->stock_quantity}).";
                }
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        $result = DB::transaction(function () use ($globalData, $request, $type): array {
            $equipment   = [];
            $consumables = [];

            foreach ($request->input('rows') as $row) {
                if ($row['kind'] === 'equipment') {
                    $equipment[] = EquipmentMovement::create([
                        'product_id'   => $row['product_id'],
                        'type'         => $type,
                        'personnel_id' => $globalData['personnel_id'],
                        'client_id'    => $globalData['client_id'] ?? null,
                        'location_id'  => $row['location_id'] ?? null,
                        'date_out'     => $globalData['date'],
                        'date_return'  => $globalData['date_return'] ?? null,
                        'notes'        => $row['notes'] ?? ($globalData['notes'] ?? null),
                    ]);
                } else {
                    $consumables[] = ConsumableMovement::create([
                        'consumable_id' => $row['consumable_id'],
                        'type'          => $type,
                        'personnel_id'  => $globalData['personnel_id'],
                        'client_id'     => $globalData['client_id'] ?? null,
                        'location_id'   => $row['location_id'] ?? null,
                        'quantity'      => $row['quantity'],
                        'movement_date' => $globalData['date'],
                        'notes'         => $row['notes'] ?? ($globalData['notes'] ?? null),
                    ]);
                }
            }

            return ['equipment' => $equipment, 'consumables' => $consumables];
        });

        $equipCount = count($result['equipment']);
        $consCount  = count($result['consumables']);
        $total      = $equipCount + $consCount;

        return response()->json([
            'message'    => "{$total} movimiento(s) registrado(s) ({$equipCount} equipo(s), {$consCount} consumible(s)).",
            'total'      => $total,
            'equipment'  => collect($result['equipment'])->map->load(['product', 'client', 'location', 'personnel']),
            'consumables'=> collect($result['consumables'])->map->load(['consumable', 'consumable.location', 'client', 'location', 'personnel']),
        ], 201);
    }
}
