<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TaxReportController;
use App\Http\Controllers\ExpenseController;

Route::middleware(['auth:sanctum', 'premium'])->get('/premium-feature', function () {
    return response()->json(['message' => 'Willkommen im Premium-Bereich!']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'API funktioniert!']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/profile/update', [ProfileController::class, 'updateProfile']);
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword']);
    //Appointments routen
    Route::get('/appointments', [AppointmentController::class, 'index']); // Alle Termine für eingeloggten Nutzer
    Route::post('/appointments', [AppointmentController::class, 'store']); // Neuen Termin erstellen
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']); // Einzelnen Termin abrufen
    Route::put('/appointments/{id}', [AppointmentController::class, 'update']); // Termin bearbeiten
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']); // Termin löschen
    Route::post('/appointments/{id}/report', [AppointmentController::class, 'saveReport']); // Terminbericht erstellen
    Route::get('/appointments/{id}/report', [AppointmentController::class, 'getReport']); // Terminbericht erstellen
    //Invoices routen
    Route::post('/invoices', [InvoiceController::class, 'store']); // Rechnung speichern
    Route::get('/invoices', [InvoiceController::class, 'index']); // Rechnungen abrufen
    Route::get('/invoices/{id}', [InvoiceController::class, 'show']); // Einzelne Rechnung abrufen 
    Route::put('/invoices/{id}', [InvoiceController::class, 'update']); // Einzelne Rechnung abrufen 


    Route::post('/invoice-items', [InvoiceItemController::class, 'store']); // Leistung hinzufügen
    Route::get('/invoice-items/{invoice_id}', [InvoiceItemController::class, 'index']); // Leistungen einer Rechnung abrufen
    Route::delete('/invoice-items/{id}', [InvoiceItemController::class, 'destroy']); // Leistung löschen

    //TODO ROUTEN

    Route::get('/todos', [TodoController::class, 'index']);
    Route::post('/todos', [TodoController::class, 'store']);
    Route::put('/todos/{id}/done', [TodoController::class, 'updateStatus']);


});

//admin middleware und admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::get('/users-with-reports', [AdminController::class, 'getUsersWithReports']);
    Route::get('/company-settings', [CompanySettingController::class, 'get']);
    Route::post('/company-settings', [CompanySettingController::class, 'store']); 
    Route::put('/company-settings/{id}', [CompanySettingController::class, 'update']); 

    //ausgaben
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/expenses/{id}', [ExpenseController::class, 'show']);
    Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);

    //rechnung pdf generierung
    Route::get('/invoices/{id}/pdf', [InvoiceController::class, 'generatePDF']);

    //steuerreport (nur einahme)
    Route::get('/reports/tax', [TaxReportController::class, 'getTaxReport']);
    //steuerreport (einahme und ausgaben)
    Route::get('/reports/finance', [TaxReportController::class, 'getFinanceReport']);
    //datev export
    Route::get('/reports/datev', [TaxReportController::class, 'getDatevReport']);
    Route::get('/reports/datev/csv', [TaxReportController::class, 'exportDatevCsv']);


    Route::get('/clients', [ClientController::class, 'index']); // Alle Clients abrufen
    Route::post('/clients', [ClientController::class, 'store']); // Neuen Client speichern
    Route::get('/clients/{id}', [ClientController::class, 'show']); // Einzelnen Client abrufen
    Route::put('/clients/{id}', [ClientController::class, 'update']); // Client aktualisieren
    Route::delete('/clients/{id}', [ClientController::class, 'destroy']); // Client löschen

});



