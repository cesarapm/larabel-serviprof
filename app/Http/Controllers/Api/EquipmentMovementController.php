<?php

namespace App\Http\Controllers\Api;

use App\Enums\InventoryStatus;
use App\Http\Controllers\Controller;
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
            ->with(['product', 'client'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($movements);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $movement = DB::transaction(function () use ($data): EquipmentMovement {
            $movement = EquipmentMovement::create($data);
            $this->syncProductInventoryStatus($movement);

            return $movement;
        });

        return response()->json($movement->load(['product', 'client']), 201);
    }

    public function show(EquipmentMovement $equipmentMovement): JsonResponse
    {
        return response()->json($equipmentMovement->load(['product', 'client']));
    }

    public function update(Request $request, EquipmentMovement $equipmentMovement): JsonResponse
    {
        $data = $request->validate($this->rules());

        DB::transaction(function () use ($equipmentMovement, $data): void {
            $equipmentMovement->update($data);
            $this->syncProductInventoryStatus($equipmentMovement->fresh());
        });

        return response()->json($equipmentMovement->fresh()->load(['product', 'client']));
    }

    public function destroy(EquipmentMovement $equipmentMovement): JsonResponse
    {
        $equipmentMovement->delete();

        return response()->json(status: 204);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'type' => ['required', Rule::in(['entrada', 'salida', 'renta', 'venta', 'mantenimiento'])],
            'date_out' => ['nullable', 'date'],
            'date_return' => ['nullable', 'date', 'after_or_equal:date_out'],
            'notes' => ['nullable', 'string'],
        ];
    }

    private function syncProductInventoryStatus(EquipmentMovement $movement): void
    {
        $product = Product::query()->find($movement->product_id);

        if (! $product) {
            return;
        }

        $status = match ($movement->type) {
            'entrada' => InventoryStatus::DISPONIBLE,
            'salida', 'renta' => InventoryStatus::RENTADO,
            'venta' => InventoryStatus::VENDIDO,
            'mantenimiento' => InventoryStatus::MANTENIMIENTO,
            default => $product->inventory_status,
        };

        if ($movement->date_return && $movement->type !== 'venta') {
            $status = InventoryStatus::DISPONIBLE;
        }

        $product->update([
            'inventory_status' => $status,
        ]);
    }
}
