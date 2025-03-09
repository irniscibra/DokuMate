<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    // Alle Ausgaben einer Firma abrufen
    public function index()
    {
        $user = auth()->user();
        
        if (!$user->company_id) {
            return response()->json(['error' => 'Keine Firma zugeordnet'], 404);
        }
    
        $expenses = Expense::where('company_id', $user->company_id)
            ->orderBy('date', 'desc')
            ->get();
    
        return response()->json($expenses);
    }

    // Einzelne Ausgabe abrufen
    public function show($id)
    {
        $expense = Expense::where('id', $id)->where('company_id', auth()->user()->company_id)->firstOrFail();
        return response()->json($expense);
    }

    // Neue Ausgabe speichern
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'recurring' => 'boolean',
            'attachment' => 'nullable|file|mimes:jpg,png,pdf|max:2048'
        ]);

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;

        // Beleg speichern
        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('expenses');
        }

        $expense = Expense::create($data);
        return response()->json($expense, 201);
    }

    // Bestehende Ausgabe aktualisieren
    public function update(Request $request, $id)
    {
        $expense = Expense::where('id', $id)->where('company_id', auth()->user()->company_id)->firstOrFail();

        $request->validate([
            'amount' => 'numeric|min:0',
            'category' => 'string|max:255',
            'description' => 'nullable|string',
            'date' => 'date',
            'recurring' => 'boolean',
            'attachment' => 'nullable|file|mimes:jpg,png,pdf|max:2048'
        ]);

        $data = $request->all();

        // Falls ein neuer Beleg hochgeladen wird, alten löschen und neuen speichern
        if ($request->hasFile('attachment')) {
            Storage::delete($expense->attachment);
            $data['attachment'] = $request->file('attachment')->store('expenses');
        }

        $expense->update($data);
        return response()->json($expense);
    }

    // Ausgabe löschen
    public function destroy($id)
    {
        $expense = Expense::where('id', $id)->where('company_id', auth()->user()->company_id)->firstOrFail();
        
        if ($expense->attachment) {
            Storage::delete($expense->attachment);
        }

        $expense->delete();
        return response()->json(['message' => 'Ausgabe gelöscht']);
    }
}
