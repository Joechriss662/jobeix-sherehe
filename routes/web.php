<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\PledgeController;
use App\Http\Controllers\ContributionController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

// Home route
Route::get('/', function () {
    return view('welcome');
});

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Resource routes
    Route::resource('events', EventController::class);
    Route::resource('invitations', InvitationController::class);
    Route::resource('guests', GuestController::class);
    Route::resource('pledges', PledgeController::class);

    // Resource route for contributions under pledges
    Route::resource('pledges.contributions', ContributionController::class);

    // Route to get guests for a specific event
    Route::get('/events/{event}/guests', [EventController::class, 'getGuests']);

    // Routes for invitation SMS preview and sending
    Route::get('/invitations/preview-sms/{id}', [InvitationController::class, 'previewSms'])->name('invitations.previewSms');
    Route::post('/invitations/send-sms/{id}', [InvitationController::class, 'sendSms'])->name('invitations.sendSms');
    //Route::post('/invitations/{id}/send-sms', [InvitationController::class, 'sendSms'])->name('invitations.sendSms');

    // Route for bulk action on invitations
    Route::post('/invitations/bulk-action', [InvitationController::class, 'bulkAction'])->name('invitations.bulkAction');

    // AJAX routes for guests
    Route::prefix('events/{event}/guests')->name('guests.')->group(function () {
        Route::post('/store', [GuestController::class, 'store'])->name('store'); // Add guest
        Route::post('/bulk-import', [GuestController::class, 'bulkImport'])->name('bulk-import'); // Bulk import guests
    });

    Route::prefix('guests/{guest}')->name('guests.')->group(function () {
        Route::put('/update', [GuestController::class, 'update'])->name('update'); // Update guest
        Route::delete('/destroy', [GuestController::class, 'destroy'])->name('destroy'); // Delete guest
    });

    // Additional route for storing guests
    Route::post('/events/{event}/guests/store', [GuestController::class, 'store'])->name('guests.store');

    // Route for updating a guest
    Route::put('/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
});

// Route for sending a test WhatsApp message
Route::get('/send-test-whatsapp', [YourController::class, 'sendTestWhatsAppMessage'])->name('whatsapp.test');

// Route for proxy status
Route::get('/proxy/status', function (Request $request) {
    $smsForwarderAddress = $request->query('address');
    try {
        $response = Http::get("$smsForwarderAddress/status");
        return response($response->body(), $response->status());
    } catch (\Exception $e) {
        return response()->json(['error' => 'Unable to connect to SMSForwarder'], 500);
    }
});

Route::post('/store-smsforwarder-address', function (Request $request) {
    $request->validate([
        'smsForwarderAddress' => 'required|url',
    ]);

    session(['smsForwarderAddress' => $request->smsForwarderAddress]);

    return response()->json(['success' => true, 'message' => 'SMSForwarder address stored successfully.']);
})->name('storeSmsForwarderAddress');

// Authentication routes
require __DIR__.'/auth.php';