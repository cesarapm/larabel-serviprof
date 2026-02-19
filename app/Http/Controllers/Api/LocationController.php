<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $locations = Location::query()
            ->with('client:id,name')
            ->withCount('products')
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($locations);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $location = Location::create($data);

        return response()->json($location->load('client'), 201);
    }

    public function show(Location $location): JsonResponse
    {
        return response()->json($location->load(['products', 'client']));
    }

    public function update(Request $request, Location $location): JsonResponse
    {
        $data = $request->validate($this->rules(true));

        $location->update($data);

        return response()->json($location->fresh()->load('client'));
    }

    public function destroy(Location $location): JsonResponse
    {
        $location->delete();

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
            'sub_location' => ['nullable', 'string', 'max:255'],
            'type' => [$requiredOrSometimes, Rule::in([
                'almacen_apodaca',
                'taller',
                'transito',
                'cliente',
                'baja_canibalizacion',
                'demo_showroom',
            ])],
            'client_id' => ['nullable', 'integer', 'exists:clients,id', Rule::requiredIf(fn () => request('type') === 'cliente')],
        ];
    }
}
