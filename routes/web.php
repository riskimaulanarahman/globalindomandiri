<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PaymentTermController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RateController;
use App\Http\Controllers\TermsAndConditionController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['web'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/ui/rates', 'ui.rates')->name('ui.rates');
    Route::view('/ui/shipments', 'ui.shipments')->name('ui.shipments');
    Route::view('/ui/invoices', 'ui.invoices')->name('ui.invoices');
    Route::view('/ui/payments', 'ui.payments')->name('ui.payments');
    Route::view('/ui/reports', 'ui.reports')->name('ui.reports');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('customers', CustomerController::class)->except(['show']);
    Route::get('customers/{customer}/contacts', [CustomerController::class, 'contacts'])->name('customers.contacts');
    Route::resource('locations', LocationController::class)->except(['show']);
    Route::resource('invoices', InvoiceController::class)->except(['show']);
    Route::post('invoices/{invoice}/lines', [InvoiceController::class, 'addLine'])->name('invoices.lines.add');
    Route::delete('invoices/{invoice}/lines/{line}', [InvoiceController::class, 'removeLine'])->name('invoices.lines.remove');
    Route::post('invoices/{invoice}/refresh', [InvoiceController::class, 'refreshFromShipments'])->name('invoices.refresh');
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::post('invoices/{invoice}/mark-sent', [InvoiceController::class, 'markSent'])->name('invoices.markSent');
    Route::resource('shipments', ShipmentController::class)->except(['show']);
    Route::post('shipments/{shipment}/create-invoice', [ShipmentController::class, 'createInvoice'])->name('shipments.createInvoice');
    Route::get('shipments/{shipment}/awb', [ShipmentController::class, 'awb'])->name('shipments.awb');
    Route::get('shipments/{shipment}/awb-barcode', [ShipmentController::class, 'awbBarcode'])->name('shipments.awb_barcode');
    Route::resource('rates', RateController::class)->except(['show']);
    Route::resource('payment-terms', PaymentTermController::class)->except(['show']);
    Route::resource('services', ServiceController::class)->except(['show']);
    // Terms & Conditions module
    Route::get('terms-conditions/options', [TermsAndConditionController::class, 'options'])->name('terms-conditions.options');
    Route::resource('terms-conditions', TermsAndConditionController::class)->except(['show']);

    Route::post('rates/import', [RateController::class, 'import'])->name('rates.import');
    Route::get('rates/export', [RateController::class, 'export'])->name('rates.export');
    Route::get('rates/options', [RateController::class, 'options'])->name('rates.options');
    Route::resource('payments', PaymentController::class)->except(['show']);
    // Quotations
    Route::resource('quotations', QuotationController::class)->except(['show']);
    Route::post('quotations/{quotation}/lines', [QuotationController::class, 'addLine'])->name('quotations.lines.add');
    Route::patch('quotations/{quotation}/lines/{line}', [QuotationController::class, 'updateLine'])->name('quotations.lines.update');
    Route::delete('quotations/{quotation}/lines/{line}', [QuotationController::class, 'removeLine'])->name('quotations.lines.remove');
    Route::post('quotations/{quotation}/lines/{line}/create-shipment', [QuotationController::class, 'createShipmentFromLine'])->name('quotations.lines.createShipment');
    Route::post('quotations/{quotation}/mark-sent', [QuotationController::class, 'markSent'])->name('quotations.markSent');
    Route::post('quotations/{quotation}/accept', [QuotationController::class, 'accept'])->name('quotations.accept');
    Route::post('quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
    Route::post('quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('quotations.convert');
    Route::post('quotations/{quotation}/close', [QuotationController::class, 'close'])->name('quotations.close');
    Route::post('quotations/{quotation}/refresh-tnc', [QuotationController::class, 'refreshTnc'])->name('quotations.refreshTnc');
    Route::get('quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');
});

require __DIR__.'/auth.php';
