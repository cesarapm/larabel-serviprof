<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consumable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsumableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $consumables = Consumable::query()
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($consumables);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $consumable = Consumable::create($data);

        return response()->json($consumable, 201);
    }

    public function show(Consumable $consumable): JsonResponse
    {
        return response()->json($consumable);
    }

    public function update(Request $request, Consumable $consumable): JsonResponse
    {
        $request->merge([
            'stock_quantity' => $request->input('stock_quantity', $consumable->stock_quantity),
        ]);

        $data = $request->validate($this->rules(true));

        $consumable->update($data);

        return response()->json($consumable->fresh());
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
            'name' => [$requiredOrSometimes, 'string', 'max:255'],
            'stock_quantity' => [$requiredOrSometimes, 'integer', 'min:0'],
            'minimum_stock' => [$requiredOrSometimes, 'integer', 'min:0'],
            'stock_reserved' => ['nullable', 'integer', 'min:0', 'lte:stock_quantity'],
            'batch' => ['nullable', 'string', 'max:255'],
            'supplier' => ['nullable', 'string', 'max:255'],
        ];
    }
}
