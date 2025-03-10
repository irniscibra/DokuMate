<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\CompanySetting;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatevExportController extends Controller
{
    /**
     * Generiert den DATEV-Export
     */
    public function exportDatevCsv(Request $request)
    {
        $period = $request->query('period', 'last_month');
        
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
        
        $companySettings = CompanySetting::first();
        $taxRate = $companySettings ? ($companySettings->tax_rate ?? 19) : 19;
        
        // Einnahmen abrufen
        $invoices = Invoice::whereBetween('invoice_date', [$from, $to])->get();
        
        // Ausgaben abrufen
        $expenses = Expense::whereBetween('date', [$from, $to])->get();
        
        $csvHeader = [
            'Belegnummer', 'Datum', 'Beschreibung', 'Kategorie', 'Betrag', 'Steuerbetrag', 'Steuersatz', 'Typ'
        ];
        
        $csvData = [];
        
        foreach ($invoices as $invoice) {
            $csvData[] = [
                'BE-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT),
                $invoice->invoice_date,
                'Rechnung ' . $invoice->invoice_number . ' - ' . $invoice->client->name,
                'Einnahme',
                number_format($invoice->total_amount, 2, ',', '.'),
                number_format($invoice->total_amount * ($taxRate / 100), 2, ',', '.'),
                $taxRate . '%',
                'Einnahme'
            ];
        }
        
        foreach ($expenses as $expense) {
            $csvData[] = [
                'BE-' . str_pad($expense->id, 6, '0', STR_PAD_LEFT),
                $expense->date,
                $expense->description,
                $expense->category,
                number_format($expense->amount, 2, ',', '.'),
                number_format($expense->amount * ($taxRate / 100), 2, ',', '.'),
                $taxRate . '%',
                'Ausgabe'
            ];
        }
        
        $callback = function () use ($csvHeader, $csvData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader, ';');
            foreach ($csvData as $row) {
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };
        
        return new StreamedResponse($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Datev-Export.csv",
        ]);
    }
}
