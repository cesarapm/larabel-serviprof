<?php

namespace App\Http\Controllers\Api;

use App\Enums\EquipmentStatus;
use App\Enums\InventoryStatus;
use App\Http\Controllers\Controller;
use App\Models\Almacen;
use App\Models\EquipmentMovement;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with(['almacen.location'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(array_merge(
            $this->rules(),
            ['personnel_id' => ['required', 'exists:personnel,id']]
        ));

        $product = DB::transaction(function () use ($data): Product {
            $product = Product::create(Arr::except($data, [
                'current_counter_bw',
                'current_counter_color',
                'counter_read_at',
                'personnel_id',
                'location_id',   // la ubicación se gestiona en almacen, no en el producto
            ]));

            // El movimiento registra la ubicación; el observer crea la fila en almacen
            EquipmentMovement::create([
                'product_id'           => $product->id,
                'client_id'            => null,
                'location_id'          => $data['location_id'],
                'personnel_id'         => $data['personnel_id'],
                'type'                 => 'entrada',
                'date_out'             => $product->entry_date,
                'date_return'          => null,
                'notes'                => 'Alta inicial de equipo',
                'current_counter_bw'   => $data['current_counter_bw'] ?? null,
                'current_counter_color' => $data['current_counter_color'] ?? null,
                'counter_read_at'      => $data['counter_read_at'] ?? null,
            ]);

            return $product;
        });

        return response()->json($product->load(['almacen.location']), 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product->load(['almacen.location', 'movements.client', 'movements.location', 'movements.personnel']));
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate($this->rules($product->id, true));

        $product->update(Arr::except($data, ['location_id']));

        return response()->json($product->fresh()->load(['almacen.location']));
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
            'type' => [$requiredOrSometimes, Rule::in(['copiadora', 'impresora', 'multifuncional_laser', 'multifuncional_tinta', 'plotter'])],
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
            'location_id' => [$requiredOrSometimes, 'exists:locations,id'],  // para la ubicación en almacen
            'entry_date' => [$requiredOrSometimes, 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
