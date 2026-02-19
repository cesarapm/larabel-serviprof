<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $clients = Client::query()
            ->withCount('equipmentMovements')
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($clients);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $client = Client::create($data);

        return response()->json($client, 201);
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json($client->load('equipmentMovements.product'));
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $data = $request->validate($this->rules($client->id));

        $client->update($data);

        return response()->json($client->fresh());
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->delete();

        return response()->json(status: 204);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?int $clientId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('clients', 'email')->ignore($clientId),
            ],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
