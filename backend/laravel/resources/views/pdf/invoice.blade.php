<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechnung {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; margin: 40px; }
        .header { text-align: center; margin-bottom: 20px; }
        .company-info, .client-info { margin-bottom: 20px; }
        .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .invoice-table th, .invoice-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .invoice-table th { background-color: #f4f4f4; }
        .total { text-align: right; font-size: 16px; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 12px; text-align: center; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Rechnung {{ $invoice->invoice_number }}</h2>
        <p><strong>Rechnungsdatum:</strong> {{ date('d.m.Y', strtotime($invoice->invoice_date)) }}</p>
    </div>


    <div class="company-info">
        <strong>Firma:</strong> {{ $invoice->company->name }} <br>
        <strong>Adresse:</strong> {{ $invoice->company->address }} <br>
        <strong>Telefon:</strong> {{ $invoice->company->phone }} <br>
        <strong>Steuer-ID:</strong> {{ $invoice->company->tax_id }}
    </div>

    <div class="client-info">
        <strong>Kunde:</strong> {{ $invoice->client->name }} <br>
        <strong>Adresse:</strong> {{ $invoice->client->address }} <br>
        <strong>E-Mail:</strong> {{ $invoice->client->email }}
    </div>
<br><br><br>
        <!-- Begrüßungstext -->
<p><strong>Sehr geehrte Damen und Herren,</strong></p>
<p>Vielen Dank für Ihren Auftrag und das damit verbundene Vertrauen.<br>
Hiermit werden folgende Leistungen in Rechnung gestellt:</p>

<br><br>
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Beschreibung</th>
                <th>Menge</th>
                <th>Einzelpreis</th>
                <th>Gesamt</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2, ',', '.') }} €</td>
                    <td>{{ number_format($item->total_price, 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Falls Mehrwertsteuerpflicht -->
    @if($invoice->company->is_taxable)
        @php
            $netto = $invoice->total_amount / 1.19;
            $steuerbetrag = $invoice->total_amount - $netto;
        @endphp
        <p class="total">Nettobetrag: {{ number_format($netto, 2, ',', '.') }} €</p>
        <p class="total">19% MwSt: {{ number_format($steuerbetrag, 2, ',', '.') }} €</p>
    @endif

    <p class="total">Gesamtbetrag: {{ number_format($invoice->total_amount, 2, ',', '.') }} €</p>
<br><br>
    <p><strong>Zahlungsziel:</strong> Bitte überweisen Sie den Gesamtbetrag innerhalb von 14 Tagen auf das untenstehende Konto.</p>

    <div class="footer">
        <p><strong>Bankverbindung:</strong><br>
        IBAN: {{ $invoice->company->iban }}<br>
        BIC: {{ $invoice->company->bic }}<br>
        Bank: {{ $invoice->company->bank_name }}</p>

        <p>Firma: {{ $invoice->company->name }} | {{ $invoice->company->address }} | Telefon: {{ $invoice->company->phone }} | E-Mail: {{ $invoice->company->email }}</p>
    </div>
</body>
</html>
