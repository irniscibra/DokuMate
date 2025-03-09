<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\CompanySetting;

class TaxReportController extends Controller
{
    /**
     * Steuerreport abrufen
     * Zeitraum kann als GET-Parameter `from` und `to` gesetzt werden
     */
    public function getTaxReport(Request $request)
    {
        // Den gewünschten Zeitraum aus der Anfrage holen
        $period = $request->query('period', 'last_month'); // Standard: Letzter Monat
    
        // Zeitraum je nach ausgewählter Option berechnen
        switch ($period) {
            case 'this_month':
                $from = now()->startOfMonth()->toDateString();
                $to = now()->endOfMonth()->toDateString();
                break;
    
            case 'last_month':
                $from = now()->subMonth()->startOfMonth()->toDateString();
                $to = now()->subMonth()->endOfMonth()->toDateString();
                break;
    
            case 'last_quarter':
                $from = now()->subMonths(3)->startOfMonth()->toDateString();
                $to = now()->subMonth()->endOfMonth()->toDateString();
                break;
    
            case 'this_year':
                $from = now()->startOfYear()->toDateString();
                $to = now()->endOfYear()->toDateString();
                break;
    
            default:
                $from = now()->startOfMonth()->toDateString();
                $to = now()->endOfMonth()->toDateString();
                break;
        }
    
        // Debugging: Überprüfen, ob die Filterung korrekt ist
        \Log::info("Steuerreport Zeitraum: $from bis $to");
    
        // Die Steuerrate aus den Firmeneinstellungen holen
        $companySettings = CompanySetting::first();
        $taxRate = $companySettings ? ($companySettings->tax_rate ?? 19) : 19; // Standard 19%
    
        // Rechnungen für den Zeitraum abrufen
        $invoices = Invoice::whereBetween('invoice_date', [$from, $to])->get();
    
        if ($invoices->isEmpty()) {
            return response()->json([
                'message' => 'Keine Rechnungen im angegebenen Zeitraum gefunden.',
                'total_revenue' => 0,
                'total_paid' => 0,
                'total_open' => 0,
                'tax_amount' => 0,
                'net_revenue' => 0
            ]);
        }
    
        // Berechnungen durchführen
        $totalRevenue = $invoices->sum('total_amount');
        $totalPaid = $invoices->where('status', 'bezahlt')->sum('total_amount');
        $totalOpen = $invoices->where('status', 'offen')->sum('total_amount');
    
        $taxAmount = $totalRevenue * ($taxRate / 100); // Steuer berechnen
        $netRevenue = $totalRevenue - $taxAmount; // Nettogewinn nach Steuer
    
        return response()->json([
            'total_revenue' => round($totalRevenue, 2),
            'total_paid' => round($totalPaid, 2),
            'total_open' => round($totalOpen, 2),
            'tax_rate' => $taxRate,
            'tax_amount' => round($taxAmount, 2),
            'net_revenue' => round($netRevenue, 2)
        ]);
    }

}
