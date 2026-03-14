<?php

namespace App\Http\Controllers\Api;

use App\Enums\EquipmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Consumable;
use App\Models\ConsumableMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ConsumableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $consumables = Consumable::query()
            ->with(['almacen.location'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($consumables);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(array_merge(
            $this->rules(),
            ['personnel_id' => ['required', 'exists:personnel,id']]
        ));

        $consumable = DB::transaction(function () use ($data): Consumable {
            // Separar campos que van a almacen (no al modelo consumable)
            $locationId  = $data['location_id'] ?? null;
            $subLocation = $data['sub_location'] ?? null;

            $consumable = Consumable::create(array_diff_key(
                $data,
                array_flip(['personnel_id', 'location_id', 'sub_location'])
            ));

            // El movimiento de entrada dispara applyAlmacenDelta → crea fila en almacen
            ConsumableMovement::create([
                'consumable_id' => $consumable->id,
                'client_id'     => null,
                'location_id'   => $locationId,
                'personnel_id'  => $data['personnel_id'],
                'type'          => 'entrada',
                'quantity'      => (int) $consumable->stock_quantity,
                'movement_date' => now()->toDateString(),
                'notes'         => 'Alta inicial de consumible',
            ]);

            // Guardar sub_location en la fila del almacen recén creada
            if ($subLocation && $locationId) {
                \App\Models\Almacen::where('consumable_id', $consumable->id)
                    ->where('location_id', $locationId)
                    ->update(['sub_location' => $subLocation]);
            }

            return $consumable;
        });

        return response()->json($consumable->load(['movements', 'almacen.location']), 201);
    }

    public function show(Consumable $consumable): JsonResponse
    {
        return response()->json($consumable->load(['almacen.location', 'movements.client', 'movements.location', 'movements.fromLocation', 'movements.personnel']));
    }

    public function update(Request $request, Consumable $consumable): JsonResponse
    {
        $request->merge([
            'stock_quantity' => $request->input('stock_quantity', $consumable->stock_quantity),
        ]);

        $data = $request->validate($this->rules(true));

        $consumable->update(array_diff_key($data, array_flip(['location_id', 'sub_location'])));

        return response()->json($consumable->fresh()->load(['almacen.location']));
    }

    public function destroy(Consumable $consumable): JsonResponse
    {
        $consumable->delete();

        return response()->json(status: 204);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(bool $isUpdate = false): array
    {
        $requiredOrSometimes = $isUpdate ? 'sometimes' : 'required';

        return [
            'type' => [$requiredOrSometimes, Rule::in(['refaccion', 'tinta', 'toner', 'otras'])],
            'name' => [$requiredOrSometimes, 'string', 'max:255'],
            'part_number' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'status' => [$requiredOrSometimes, Rule::in(array_column(EquipmentStatus::cases(), 'value'))],
            'unit' => [$requiredOrSometimes, Rule::in(['pieza', 'caja', 'kit', 'litro', 'ml'])],
            'stock_quantity' => [$requiredOrSometimes, 'integer', 'min:0'],
            'minimum_stock' => [$requiredOrSometimes, 'integer', 'min:0'],
            'stock_reserved' => ['nullable', 'integer', 'min:0', 'lte:stock_quantity'],
            'batch' => ['nullable', 'string', 'max:255'],
            'supplier' => ['nullable', 'string', 'max:255'],
            // location_id y sub_location van a tabla almacen (sólo para store)
            'location_id' => ['nullable', 'exists:locations,id'],
            'sub_location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'inventory_status' => [$requiredOrSometimes, Rule::in(['disponible', 'rentado', 'vendido', 'mantenimiento'])],
        ];
    }
}
