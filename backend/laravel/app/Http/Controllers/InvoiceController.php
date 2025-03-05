<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{

    public function index()
    {
        return response()->json(Invoice::with(['company', 'client', 'user'])->get());
    }

   
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'company_id' => 'required|exists:companies,id',
    //         'client_id' => 'nullable|exists:clients,id',
    //         'user_id' => 'required|exists:users,id',
    //         'invoice_date' => 'required|date',
    //         'total_amount' => 'required|numeric|min:0',
    //         'status' => 'required|string|in:offen,bezahlt,storniert',
    //     ]);

       
    //     $invoiceNumber = 'RECH-' . Str::random(8);

    //     // Rechnung speichern
    //     $invoice = Invoice::create([
    //         'company_id' => $request->company_id,
    //         'client_id' => $request->client_id,
    //         'user_id' => $request->user_id,
    //         'invoice_number' => $invoiceNumber,
    //         'invoice_date' => $request->invoice_date,
    //         'total_amount' => $request->total_amount,
    //         'status' => $request->status,
    //     ]);

    //     return response()->json($invoice, 201);
    // }

  

    public function store(Request $request)
    {
        Log::info('POST /api/invoices wurde aufgerufen.', ['data' => $request->all()]);
    
        try {
            $request->validate([
                'company_id' => 'required|exists:companies,id',
                'client_id' => 'nullable|exists:clients,id',
                'user_id' => 'required|exists:users,id',
                'invoice_date' => 'required|date',
                'total_amount' => 'required|numeric|min:0',
                'status' => 'required|string|in:offen,bezahlt,storniert',
            ]);
    
            $invoiceNumber = 'RECH-' . now()->format('Ymd') . '-' . rand(1000, 9999);
            
            Log::info('Validierung erfolgreich. Speichere Rechnung...');
    
            $invoice = Invoice::create([
                'company_id' => $request->company_id,
                'client_id' => $request->client_id,
                'user_id' => $request->user_id,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $request->invoice_date,
                'total_amount' => $request->total_amount,
                'status' => $request->status,
            ]);
    
            Log::info('Rechnung erfolgreich gespeichert!', ['invoice' => $invoice]);
    
            return response()->json($invoice, 201);
    
        } catch (\Exception $e) {
            Log::error('Fehler beim Erstellen der Rechnung:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Rechnung konnte nicht erstellt werden.'], 500);
        }
    }

}
