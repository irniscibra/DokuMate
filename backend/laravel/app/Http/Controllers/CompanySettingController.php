<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanySetting;

class CompanySettingController extends Controller
{
    // GET: Holen der Firmeneinstellungen
    public function get()
    {
        return response()->json(CompanySetting::first());
    }

    // POST: Erstellt neue Firmeneinstellungen (nur falls noch nicht existiert)
    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'tax_id' => 'required|string',
            'ceo_name' => 'required|string',
            'bank_details' => 'nullable|string',
            'bic' => 'nullable|string', 
            'bank_name' => 'nullable|string', 
            'invoice_footer' => 'nullable|string',
        ]);

        // Prüfen, ob bereits Firmeneinstellungen existieren
        if (CompanySetting::exists()) {
            return response()->json(['message' => 'Firmeneinstellungen existieren bereits.'], 409);
        }

        // Neue Firmeneinstellungen speichern
        $settings = CompanySetting::create($request->all());
        return response()->json($settings, 201);
    }

    // PUT: Aktualisiert vorhandene Firmeneinstellungen
    public function update(Request $request, $id)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'tax_id' => 'required|string',
            'ceo_name' => 'required|string',
            'bank_details' => 'nullable|string',
            'bic' => 'nullable|string', 
            'bank_name' => 'nullable|string', 
            'invoice_footer' => 'nullable|string',
        ]);

        // Firmeneinstellungen mit der ID finden
        $settings = CompanySetting::findOrFail($id);
        $settings->update($request->all());

        return response()->json($settings);
    }
}
