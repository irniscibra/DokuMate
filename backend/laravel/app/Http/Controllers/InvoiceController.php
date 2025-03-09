<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{

    public function index()
    {
        return response()->json(Invoice::with(['company', 'client', 'user'])->get());
    }

   
    public function show($id)
{
    $invoice = Invoice::with(['company', 'client', 'user', 'items'])->find($id);

    if (!$invoice) {
        return response()->json(['message' => 'Rechnung nicht gefunden'], 404);
    }

    return response()->json($invoice);
}

 

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
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
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

        // 📝 **Leistungen speichern**
        foreach ($request->items as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return response()->json($invoice->load('items'), 201);

    } catch (\Exception $e) {
        Log::error('Fehler beim Erstellen der Rechnung:', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Rechnung konnte nicht erstellt werden.'], 500);
    }
}

public function update(Request $request, $id)
{
    $invoice = Invoice::find($id);
    if (!$invoice) {
        return response()->json(['error' => 'Rechnung nicht gefunden'], 404);
    }

    $invoice->update(['status' => $request->status]);

    return response()->json($invoice);
}

public function delete($id)
{
    $invoice = Invoice::find($id);

    if (!$invoice) {
        return response()->json(['message' => 'Rechnung nicht gefunden'], 404);
    }

    $invoice->delete();

    return response()->json(['message' => 'Rechnung gelöscht'], 200);


}


public function generatePDF($id)
{
    $invoice = Invoice::with(['company', 'client', 'user', 'items'])->find($id);

    if (!$invoice) {
        return response()->json(['error' => 'Rechnung nicht gefunden'], 404);
    }

    $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));

    return $pdf->download("{$invoice->invoice_number}.pdf");
}

}
