<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
 
    public function index()
    {
        return response()->json(Client::all());
    }

  
    public function show($id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['error' => 'Client nicht gefunden'], 404);
        }
        return response()->json($client);
    }


    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $client = Client::create($request->all());
        return response()->json($client, 201);
    }


    public function update(Request $request, $id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['error' => 'Client nicht gefunden'], 404);
        }

        $request->validate([
            'name' => 'string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $client->update($request->all());
        return response()->json($client);
    }

 
    public function destroy($id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['error' => 'Client nicht gefunden'], 404);
        }

        $client->delete();
        return response()->json(['message' => 'Client gelöscht']);
    }
}
