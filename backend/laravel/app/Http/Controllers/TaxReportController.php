<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\CompanySetting;
use Barryvdh\DomPDFFacade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    // public function getFinanceReport(Request $request)
    // {
    //     $from = $request->query('from', now()->startOfMonth()->toDateString());
    //     $to = $request->query('to', now()->endOfMonth()->toDateString());
    
    //     $user = auth()->user();
    //     if (!$user->company_id) {
    //         return response()->json(['error' => 'Keine Firma zugeordnet'], 404);
    //     }
    
    //     // Einnahmen aus Rechnungen
    //     $invoices = Invoice::where('company_id', $user->company_id)
    //                         ->whereBetween('invoice_date', [$from, $to])
    //                         ->get();
    
    //     $totalRevenue = $invoices->sum('total_amount');
    //     $totalPaid = $invoices->where('status', 'bezahlt')->sum('total_amount');
    //     $totalOpen = $invoices->where('status', 'offen')->sum('total_amount');
    
    //     // Umsatzsteuer (19 % auf Einnahmen)
    //     $taxRate = 19;
    //     $taxAmount = $totalRevenue * ($taxRate / 100);
    //     $netRevenue = $totalRevenue - $taxAmount;
    
    //     // **Ausgaben**
    //     $expenses = Expense::where('company_id', $user->company_id)
    //                        ->whereBetween('date', [$from, $to])
    //                        ->get();
    
    //     $totalExpenses = $expenses->sum('amount');
    
    //     // Gewinnberechnung
    //     $profitBeforeTax = $totalRevenue - $totalExpenses;
    //     $profitAfterTax = $profitBeforeTax - $taxAmount;
    
    //     return response()->json([
    //         'total_revenue' => round($totalRevenue, 2),
    //         'total_paid' => round($totalPaid, 2),
    //         'total_open' => round($totalOpen, 2),
    //         'tax_rate' => $taxRate,
    //         'tax_amount' => round($taxAmount, 2),
    //         'net_revenue' => round($netRevenue, 2),
    //         'total_expenses' => round($totalExpenses, 2),
    //         'profit_before_tax' => round($profitBeforeTax, 2),
    //         'profit_after_tax' => round($profitAfterTax, 2),
    //     ]);
    // }
    
    public function getFinanceReport(Request $request)
    {
        // Standardwerte setzen, falls keine Parameter übergeben wurden
        $period = $request->query('period', 'last_month');
    
        // Zeitraum je nach gewähltem Zeitraum berechnen
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
                return response()->json(['error' => 'Ungültiger Zeitraum'], 400);
        }
    
        // Debugging: Logge den Zeitraum zur Kontrolle
        \Log::info("Steuerreport Zeitraum: $from bis $to");
    
        // Nutzer überprüfen
        $user = auth()->user();
        if (!$user || !$user->company_id) {
            return response()->json(['error' => 'Nicht authentifiziert oder keine Firma zugeordnet'], 403);
        }
    
        // Einnahmen abrufen
        $invoices = Invoice::where('company_id', $user->company_id)
                            ->whereBetween('invoice_date', [$from, $to])
                            ->get();
    
        // Einnahmen summieren
        $totalRevenue = $invoices->sum('total_amount');
        $totalPaid = $invoices->where('status', 'bezahlt')->sum('total_amount');
        $totalOpen = $invoices->where('status', 'offen')->sum('total_amount');
    
        // Standard-Steuersatz (Falls nicht in `CompanySetting` gespeichert)
        $companySettings = CompanySetting::first();
        $taxRate = $companySettings ? ($companySettings->tax_rate ?? 19) : 19; 
        $taxAmount = $totalRevenue * ($taxRate / 100);
        $netRevenue = $totalRevenue - $taxAmount;
    
        // **Ausgaben berechnen**
        $expenses = Expense::where('company_id', $user->company_id)
                            ->whereBetween('date', [$from, $to])
                            ->get();
    
        $totalExpenses = $expenses->sum('amount');
    
        // Gewinn berechnen
        $profitBeforeTax = $totalRevenue - $totalExpenses;
        $profitAfterTax = $profitBeforeTax - $taxAmount;
    
        return response()->json([
            'total_revenue' => round($totalRevenue, 2),
            'total_paid' => round($totalPaid, 2),
            'total_open' => round($totalOpen, 2),
            'tax_rate' => $taxRate,
            'tax_amount' => round($taxAmount, 2),
            'net_revenue' => round($netRevenue, 2),
            'total_expenses' => round($totalExpenses, 2),
            'profit_before_tax' => round($profitBeforeTax, 2),
            'profit_after_tax' => round($profitAfterTax, 2),
        ]);
    }
    
    public function exportFinancePdf(Request $request)
    {
        $from = $request->query('from', now()->startOfMonth()->toDateString());
        $to = $request->query('to', now()->endOfMonth()->toDateString());
    
        $user = auth()->user();
        if (!$user->company_id) {
            return response()->json(['error' => 'Keine Firma zugeordnet'], 404);
        }
    
        $financeData = $this->getFinanceData($user->company_id, $from, $to);
    
        $pdf = Pdf::loadView('pdf.finance-report', compact('financeData', 'from', 'to'));
        return $pdf->download('Steuerreport.pdf');
    }
    
    public function exportFinanceCsv(Request $request)
    {
        $from = $request->query('from', now()->startOfMonth()->toDateString());
        $to = $request->query('to', now()->endOfMonth()->toDateString());
    
        $user = auth()->user();
        if (!$user->company_id) {
            return response()->json(['error' => 'Keine Firma zugeordnet'], 404);
        }
    
        $financeData = $this->getFinanceData($user->company_id, $from, $to);
    
        $csvHeader = ["Kategorie", "Betrag", "Datum"];
        $csvData = [];
        
        foreach ($financeData['expenses'] as $expense) {
            $csvData[] = [$expense['category'], $expense['amount'], $expense['date']];
        }
    
        foreach ($financeData['invoices'] as $invoice) {
            $csvData[] = ["Einnahme - " . $invoice['client'], $invoice['total_amount'], $invoice['invoice_date']];
        }
    
        $callback = function () use ($csvHeader, $csvData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
    
        return new StreamedResponse($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Steuerreport.csv",
        ]);
    }
    
    // datev export 

    public function getDatevReport(Request $request)
    {
        $period = $request->query('period', 'last_month');
        [$from, $to] = $this->getDateRange($period);

        $invoices = Invoice::whereBetween('invoice_date', [$from, $to])->get();
        $expenses = Expense::whereBetween('date', [$from, $to])->get();

        $datevData = [];

        // 📌 Rechnungen für DATEV aufbereiten
        foreach ($invoices as $invoice) {
            $datevData[] = [
                'date' => $invoice->invoice_date,
                'document_number' => "RE-" . str_pad($invoice->id, 6, '0', STR_PAD_LEFT),
                'description' => 'Rechnung an ' . ($invoice->client ? $invoice->client->name : 'Unbekannt'),
                'tax_rate' => 19, // Fix für DATEV
                'amount' => $invoice->total_amount
            ];
        }

        // 📌 Ausgaben für DATEV aufbereiten
        foreach ($expenses as $expense) {
            $datevData[] = [
                'date' => $expense->date,
                'document_number' => "EX-" . str_pad($expense->id, 6, '0', STR_PAD_LEFT),
                'description' => 'Ausgabe: ' . $expense->category,
                'tax_rate' => 19, // Fix für DATEV
                'amount' => -1 * $expense->amount // Negative Werte für Ausgaben
            ];
        }

        return response()->json($datevData);
    }


    public function exportDatevCsv(Request $request)
    {
        $period = $request->query('period', 'last_month');
        [$from, $to] = $this->getDateRange($period);

        $invoices = Invoice::whereBetween('invoice_date', [$from, $to])->get();
        $expenses = Expense::whereBetween('date', [$from, $to])->get();

        $csvHeader = ["Datum", "Belegnummer", "Beschreibung", "Steuersatz", "Betrag (€)"];
        $csvData = [];

        // Rechnungen
        foreach ($invoices as $invoice) {
            $csvData[] = [
                $invoice->invoice_date,
                "RE-" . str_pad($invoice->id, 6, '0', STR_PAD_LEFT),
                'Rechnung an ' . ($invoice->client ? $invoice->client->name : 'Unbekannt'),
                19,
                $invoice->total_amount
            ];
        }

        // Ausgaben
        foreach ($expenses as $expense) {
            $csvData[] = [
                $expense->date,
                "EX-" . str_pad($expense->id, 6, '0', STR_PAD_LEFT),
                'Ausgabe: ' . $expense->category,
                19,
                -1 * $expense->amount
            ];
        }

        $callback = function () use ($csvHeader, $csvData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=datev_export.csv",
        ]);
    }


    private function getDateRange($period)
    {
        switch ($period) {
            case 'this_month':
                return [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()];
            case 'last_month':
                return [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()];
            case 'last_quarter':
                return [now()->subMonths(3)->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()];
            case 'this_year':
                return [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()];
            default:
                return [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()];
        }
    }


}
