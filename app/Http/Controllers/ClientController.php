<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientIndexRequest;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientController extends Controller
{
    public function index(ClientIndexRequest $request)
    {
        if ($request->expectsJson()) {
            $clients = Client::search($request->validated('q'))
                ->ofStatus($request->status())
                ->latest('id')
                ->paginate(10);

            return ClientResource::collection($clients);
        }

        return view('clients.index');
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

    public function show(Client $client)
    {
        $client->load(['subscriptions.plan', 'subscriptions.payments']);
        $payments = $client->subscriptions->flatMap->payments->sortByDesc('payment_date');

        return view('clients.show', compact('client', 'payments'));
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
        if ($client->subscriptions()->exists()) {
            return response()->json([
                'data' => null,
                'message' => 'No se puede eliminar un cliente con suscripciones.',
            ], 422);
        }

        $client->delete();

        return response()->json([
            'data' => null,
            'message' => 'Cliente eliminado exitosamente.',
        ]);
    }

    public function export(ClientIndexRequest $request): StreamedResponse
    {
        $query = Client::search($request->validated('q'))
            ->ofStatus($request->status());

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM para que Excel lea bien las tildes
            fputcsv($out, ['ID', 'Nombre', 'Correo', 'Teléfono', 'Dirección', 'Estado', 'Creado'], ';', '"', '\\');

            foreach ($query->lazyById(500) as $client) {
                fputcsv($out, [
                    $client->id,
                    $client->name,
                    $client->email,
                    $client->phone,
                    $client->address,
                    $client->status->label(),
                    $client->created_at->format('Y-m-d H:i'),
                ], ';', '"', '\\');
            }

            fclose($out);
        }, 'clientes-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
