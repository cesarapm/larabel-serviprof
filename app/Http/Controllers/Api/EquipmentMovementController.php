<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\InventoryStatus;
use App\Models\EquipmentMovement;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EquipmentMovementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $movements = EquipmentMovement::query()
            ->with(['product', 'client', 'location', 'personnel'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($movements);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $movement = DB::transaction(fn (): EquipmentMovement => EquipmentMovement::create($data));

        return response()->json($movement->load(['product', 'client', 'location', 'personnel']), 201);
    }

    public function show(EquipmentMovement $equipmentMovement): JsonResponse
    {
        return response()->json($equipmentMovement->load(['product', 'client', 'location', 'personnel']));
    }

    public function update(Request $request, EquipmentMovement $equipmentMovement): JsonResponse
    {
        $data = $request->validate($this->rules(true));

        DB::transaction(fn () => $equipmentMovement->update($data));

        return response()->json($equipmentMovement->fresh()->load(['product', 'client', 'location', 'personnel']));
    }

    public function destroy(EquipmentMovement $equipmentMovement): JsonResponse
    {
        $equipmentMovement->delete();

        return response()->json(status: 204);
    }

    /**
     * POST /api/equipment-movements/{equipmentMovement}/retorno
     *
     * Registra el retorno de un equipo que estaba en renta o mantenimiento.
     * Crea un nuevo movimiento 'retorno' y cierra el movimiento original
     * marcando su date_return.
     */
    public function retorno(Request $request, EquipmentMovement $equipmentMovement): JsonResponse
    {
        if (! in_array($equipmentMovement->type, ['renta', 'mantenimiento', 'salida'])) {
            return response()->json([
                'message' => "Solo se puede registrar retorno de movimientos de tipo renta, mantenimiento o salida. Tipo actual: {$equipmentMovement->type}.",
            ], 422);
        }

        $data = $request->validate([
            'personnel_id'        => ['nullable', 'exists:personnel,id'],
            'location_id'         => ['nullable', 'exists:locations,id'],
            'date_return'         => ['required', 'date'],
            'current_counter_bw'  => ['nullable', 'integer', 'min:0'],
            'current_counter_color' => ['nullable', 'integer', 'min:0'],
            'counter_read_at'     => ['nullable', 'date'],
            'notes'               => ['nullable', 'string'],
        ]);

        $movement = DB::transaction(function () use ($data, $equipmentMovement): EquipmentMovement {
            // Cerrar el movimiento original con la fecha de retorno
            $equipmentMovement->update(['date_return' => $data['date_return']]);

            // Si no se manda personal, se usa el del movimiento original
            $personnelId = $data['personnel_id'] ?? $equipmentMovement->personnel_id;

            // Crear nuevo movimiento retorno para trazabilidad completa
            return EquipmentMovement::create([
                'product_id'            => $equipmentMovement->product_id,
                'client_id'             => $equipmentMovement->client_id,
                'location_id'           => $data['location_id'] ?? $equipmentMovement->location_id,
                'personnel_id'          => $personnelId,
                'type'                  => 'retorno',
                'date_out'              => $data['date_return'],
                'date_return'           => null,
                'current_counter_bw'    => $data['current_counter_bw'] ?? null,
                'current_counter_color' => $data['current_counter_color'] ?? null,
                'counter_read_at'       => $data['counter_read_at'] ?? null,
                'notes'                 => $data['notes'] ?? null,
            ]);
        });

        return response()->json([
            'message'           => 'Retorno registrado correctamente.',
            'retorno_movement'  => $movement->load(['product', 'client', 'location', 'personnel']),
            'original_movement' => $equipmentMovement->fresh()->load(['product', 'client', 'location', 'personnel']),
        ], 201);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(bool $isUpdate = false): array
    {
        $requiredOrSometimes = $isUpdate ? 'sometimes' : 'required';

        return [
            'product_id' => [
                $requiredOrSometimes,
                'exists:products,id',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (request('type') === 'movimiento_interno' && $value) {
                        $product = Product::find($value);
                        if ($product && $product->inventory_status !== InventoryStatus::DISPONIBLE) {
                            $fail('El equipo no está disponible para mover (estado actual: ' . $product->inventory_status->value . ').');
                        }
                    }
                },
            ],
            'client_id' => ['nullable', 'exists:clients,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'personnel_id' => [$requiredOrSometimes, 'exists:personnel,id'],
            'type' => [$requiredOrSometimes, Rule::in(['entrada', 'salida', 'renta', 'venta', 'mantenimiento', 'movimiento_interno', 'retorno'])],
            'current_counter_bw' => ['nullable', 'integer', 'min:0'],
            'current_counter_color' => ['nullable', 'integer', 'min:0'],
            'counter_read_at' => ['nullable', 'date'],
            'date_out' => ['nullable', 'date'],
            'date_return' => ['nullable', 'date', 'after_or_equal:date_out'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
