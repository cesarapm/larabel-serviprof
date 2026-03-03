<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipmentMovement;
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
     * @return array<string, mixed>
     */
    private function rules(bool $isUpdate = false): array
    {
        $requiredOrSometimes = $isUpdate ? 'sometimes' : 'required';

        return [
            'product_id' => [$requiredOrSometimes, 'exists:products,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'personnel_id' => [$requiredOrSometimes, 'exists:personnel,id'],
            'type' => [$requiredOrSometimes, Rule::in(['entrada', 'salida', 'renta', 'venta', 'mantenimiento'])],
            'current_counter_bw' => ['nullable', 'integer', 'min:0'],
            'current_counter_color' => ['nullable', 'integer', 'min:0'],
            'counter_read_at' => ['nullable', 'date'],
            'date_out' => ['nullable', 'date'],
            'date_return' => ['nullable', 'date', 'after_or_equal:date_out'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
