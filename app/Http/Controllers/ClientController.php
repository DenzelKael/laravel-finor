<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::latest()->paginate(10);

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(ClientRequest $request)
    {
        $client = Client::create($request->validated());

        return (new ClientResource($client))
            ->additional(['message' => 'Cliente creado exitosamente.'])
            ->response()
            ->setStatusCode(201);
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(ClientRequest $request, Client $client)
    {
        $client->update($request->validated());

        return (new ClientResource($client))
            ->additional(['message' => 'Cliente actualizado exitosamente.'])
            ->response();
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json([
            'data'    => null,
            'message' => 'Cliente eliminado exitosamente.',
        ]);
    }
}
