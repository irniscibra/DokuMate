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
    Route::post('/invoice-items', [InvoiceItemController::class, 'store']); // Leistung hinzufügen
    Route::get('/invoice-items/{invoice_id}', [InvoiceItemController::class, 'index']); // Leistungen einer Rechnung abrufen
    Route::delete('/invoice-items/{id}', [InvoiceItemController::class, 'destroy']); // Leistung löschen


});

//admin middleware und admin routes


Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::get('/users-with-reports', [AdminController::class, 'getUsersWithReports']);
    Route::get('/company-settings', [CompanySettingController::class, 'get']);
    Route::post('/company-settings', [CompanySettingController::class, 'store']); 
    Route::put('/company-settings/{id}', [CompanySettingController::class, 'update']); 


    Route::get('/clients', [ClientController::class, 'index']); // Alle Clients abrufen
    Route::post('/clients', [ClientController::class, 'store']); // Neuen Client speichern
    Route::get('/clients/{id}', [ClientController::class, 'show']); // Einzelnen Client abrufen
    Route::put('/clients/{id}', [ClientController::class, 'update']); // Client aktualisieren
    Route::delete('/clients/{id}', [ClientController::class, 'destroy']); // Client löschen

});



