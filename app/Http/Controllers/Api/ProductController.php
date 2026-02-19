<?php

namespace App\Http\Controllers\Api;

use App\Enums\EquipmentStatus;
use App\Enums\InventoryStatus;
use App\Http\Controllers\Controller;
use App\Models\EquipmentMovement;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with(['location'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $product = DB::transaction(function () use ($data): Product {
            $product = Product::create($data);

            EquipmentMovement::create([
                'product_id' => $product->id,
                'client_id' => null,
                'type' => 'entrada',
                'date_out' => $product->entry_date,
                'date_return' => null,
                'notes' => 'Alta inicial de equipo',
            ]);

            return $product;
        });

        return response()->json($product->load('location'), 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product->load(['location', 'movements.client']));
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate($this->rules($product->id, true));

        $product->update($data);

        return response()->json($product->fresh()->load('location'));
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(status: 204);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?int $productId = null, bool $isUpdate = false): array
    {
        $requiredOrSometimes = $isUpdate ? 'sometimes' : 'required';

        return [
            'type' => [$requiredOrSometimes, Rule::in(['copiadora', 'impresora'])],
            'brand' => [$requiredOrSometimes, 'string', 'max:255'],
            'model' => [$requiredOrSometimes, 'string', 'max:255'],
            'serial_number' => [
                $requiredOrSometimes,
                'string',
                'max:255',
                Rule::unique('products', 'serial_number')->ignore($productId),
            ],
            'spd_internal_id' => [
                $requiredOrSometimes,
                'string',
                'max:255',
                Rule::unique('products', 'spd_internal_id')->ignore($productId),
            ],
            'current_counter_bw' => ['nullable', 'integer', 'min:0'],
            'current_counter_color' => ['nullable', 'integer', 'min:0'],
            'counter_read_at' => ['nullable', 'date'],
            'status' => [$requiredOrSometimes, Rule::in(array_column(EquipmentStatus::cases(), 'value'))],
            'inventory_status' => ['nullable', Rule::in(array_column(InventoryStatus::cases(), 'value'))],
            'classification' => [$requiredOrSometimes, Rule::in(['renta', 'venta', 'refaccion', 'demo', 'taller'])],
            'commercial_condition' => [$requiredOrSometimes, Rule::in(['a1', 'a2', 'b', 'c'])],
            'acquisition_cost' => [$requiredOrSometimes, 'numeric', 'min:0'],
            'supplier' => [$requiredOrSometimes, 'string', 'max:255'],
            'acquisition_date' => [$requiredOrSometimes, 'date'],
            'book_value' => ['nullable', 'numeric', 'min:0'],
            'depreciation_amount' => ['nullable', 'numeric', 'min:0'],
            'location_id' => [$requiredOrSometimes, 'exists:locations,id'],
            'entry_date' => [$requiredOrSometimes, 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
