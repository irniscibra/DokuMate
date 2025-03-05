<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceItem;

class InvoiceItemController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'invoice_id' => 'required|exists:invoices,id',
        'items' => 'required|array',
        'items.*.description' => 'required|string',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
    ]);

    $invoice = Invoice::find($request->invoice_id);

    $totalAmount = 0; // Gesamtsumme der Rechnung

    foreach ($request->items as $item) {
        $total_price = $item['quantity'] * $item['unit_price'];
        $totalAmount += $total_price;

        InvoiceItem::create([
            'invoice_id' => $request->invoice_id,
            'description' => $item['description'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
            'total_price' => $total_price
        ]);
    }

    // Rechnungssumme aktualisieren
    $invoice->total_amount = $totalAmount;
    $invoice->save();

    return response()->json(['message' => 'Leistungen gespeichert', 'total_amount' => $totalAmount], 201);
}

    public function index($invoice_id)
    {
        return response()->json(InvoiceItem::where('invoice_id', $invoice_id)->get());
    }

    public function destroy($id)
    {
        $item = InvoiceItem::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Leistung gelöscht']);
    }
}
