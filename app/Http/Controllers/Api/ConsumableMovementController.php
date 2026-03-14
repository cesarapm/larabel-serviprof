<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consumable;
use App\Models\ConsumableMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConsumableMovementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $movements = ConsumableMovement::query()
            ->with(['consumable', 'consumable.almacen.location', 'client', 'location', 'fromLocation', 'personnel'])
            ->latest('movement_date')
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($movements);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $movement = ConsumableMovement::create($data);

        return response()->json($movement->load(['consumable', 'consumable.almacen.location', 'client', 'location', 'fromLocation', 'personnel']), 201);
    }

    public function show(ConsumableMovement $consumableMovement): JsonResponse
    {
        return response()->json($consumableMovement->load(['consumable', 'consumable.almacen.location', 'client', 'location', 'fromLocation', 'personnel']));
    }

    public function update(Request $request, ConsumableMovement $consumableMovement): JsonResponse
    {
        $data = $request->validate($this->rules(true));

        $consumableMovement->update($data);

        return response()->json($consumableMovement->fresh()->load(['consumable', 'consumable.almacen.location', 'client', 'location', 'fromLocation', 'personnel']));
    }

    public function destroy(ConsumableMovement $consumableMovement): JsonResponse
    {
        $consumableMovement->delete();

        return response()->json(status: 204);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(bool $isUpdate = false): array
    {
        $requiredOrSometimes = $isUpdate ? 'sometimes' : 'required';

        return [
            'consumable_id' => [$requiredOrSometimes, 'exists:consumables,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'from_location_id' => [
                Rule::requiredIf(fn () => request('type') === 'movimiento_interno'),
                'nullable',
                'exists:locations,id',
            ],
            'personnel_id' => [$requiredOrSometimes, 'exists:personnel,id'],
            'type' => [$requiredOrSometimes, Rule::in(['entrada', 'salida', 'ajuste', 'movimiento_interno', 'vendido'])],
            'quantity' => [
                $requiredOrSometimes,
                'integer',
                'min:1',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $type = request('type');
                    if (in_array($type, ['salida', 'movimiento_interno', 'vendido'])) {
                        $consumableId = request('consumable_id');
                        $consumable = $consumableId ? Consumable::find($consumableId) : null;
                        if ($consumable && $value > $consumable->stock_quantity) {
                            $fail("La cantidad ({$value}) supera el stock disponible ({$consumable->stock_quantity}).");
                        }
                    }
                },
            ],
            'movement_date' => [$requiredOrSometimes, 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
