<?php

use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FlightTicketController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\VisaCountryController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\VisaApplicationController;
use App\Http\Controllers\VisaController;
use App\Models\Package;
use App\Models\VisaApplication;
use App\Models\VisaCountry;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public website (Blade — SEO-first)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Inertia::render('Public/Home', [
        'visas' => collect(VisaCountry::publicConfig())->map(fn ($v, $slug) => [
            'slug' => $slug, 'name' => $v['name'], 'flag' => $v['flag'],
            'processing' => $v['processing'], 'fee_from' => $v['fee_from'],
        ])->values(),
        'packages' => Package::activeOrdered()->take(3)->get()
            ->map(fn ($p) => $p->only(['slug', 'title', 'nights', 'price', 'tag', 'color'])),
    ]);
})->name('home');

// Visa processing
Route::get('/visa', [VisaController::class, 'index'])->name('visa.index');

// Multi-step application wizard (declared before the {country} catch-all)
Route::get('/visa/{country}/apply', [VisaApplicationController::class, 'create'])->name('visa.apply');
Route::post('/visa/{country}/apply', [VisaApplicationController::class, 'store'])->middleware('throttle:8,1')->name('visa.apply.store');
Route::get('/visa/{country}/apply/success', [VisaApplicationController::class, 'success'])->name('visa.apply.success');

Route::get('/visa/{country}', [VisaController::class, 'show'])->name('visa.show');

// Air tickets (request-based)
Route::get('/air-tickets', [InquiryController::class, 'tickets'])->name('tickets.index');
Route::post('/air-tickets', [InquiryController::class, 'storeTicket'])->middleware('throttle:8,1')->name('tickets.store');

// Tour packages
Route::get('/packages', [InquiryController::class, 'packages'])->name('packages.index');
Route::get('/packages/{package:slug}/book', [InquiryController::class, 'bookPackage'])->name('packages.book');
Route::post('/packages/{package:slug}/book', [InquiryController::class, 'storePackageBooking'])->middleware('throttle:8,1')->name('packages.book.store');

// Contact / general inquiries
Route::get('/contact', [InquiryController::class, 'contact'])->name('contact');
Route::post('/contact', [InquiryController::class, 'storeContact'])->middleware('throttle:8,1')->name('contact.store');

// Track application status (public)
Route::get('/track', [TrackController::class, 'show'])->name('track');
Route::post('/track', [TrackController::class, 'check'])->middleware('throttle:6,1')->name('track.check');

Route::redirect('/about', '/')->name('about');

// Legal
Route::get('/terms', fn () => Inertia::render('Public/Legal/Terms'))->name('terms');
Route::get('/privacy', fn () => Inertia::render('Public/Legal/Privacy'))->name('privacy');

/*
|--------------------------------------------------------------------------
| Client portal (Inertia + Vue — behind auth)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = auth()->user();

    // Only the signed-in user's own applications (match by account, never by
    // unverified email — otherwise anyone could register with someone's email
    // and see their applications).
    $applications = VisaApplication::query()
        ->where('user_id', $user->id)
        ->latest()
        ->get()
        ->map(fn ($a) => [
            'reference' => $a->reference,
            'country' => $a->countryName(),
            'flag' => $a->countryFlag(),
            'visa_type' => $a->visa_type,
            'status' => $a->status,
            'created' => $a->created_at->format('d M Y'),
        ]);

    return Inertia::render('Dashboard', ['applications' => $applications]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Customer invoices
    Route::get('/invoices', [\App\Http\Controllers\PortalInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\PortalInvoiceController::class, 'pdf'])->name('invoices.pdf');

    // Customer e-tickets (named my-tickets.* to avoid clashing with the public air-tickets page)
    Route::get('/my-tickets', [\App\Http\Controllers\PortalTicketController::class, 'index'])->name('my-tickets.index');
    Route::get('/my-tickets/{flightTicket}/pdf', [\App\Http\Controllers\PortalTicketController::class, 'pdf'])->name('my-tickets.pdf');
});

/*
|--------------------------------------------------------------------------
| Admin portal (Inertia + Vue — separate "admin" auth guard)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Staff login (admin guard) — accessible without the customer session
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [\App\Http\Controllers\Admin\SearchController::class, 'index'])->name('search');
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}/edit', [ApplicationController::class, 'edit'])->name('applications.edit');
    Route::patch('/applications/{application}', [ApplicationController::class, 'update'])->name('applications.update');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::patch('/applications/{application}/status', [ApplicationController::class, 'updateStatus'])->name('applications.status');
    Route::get('/applications/{application}/email', [ApplicationController::class, 'compose'])->name('applications.compose');
    Route::post('/applications/{application}/message', [ApplicationController::class, 'message'])->name('applications.message');
    Route::get('/applications/{application}/document/{type}', [ApplicationController::class, 'document'])->name('applications.document');

    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::patch('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.status');

    // Clients (order: literal /create before /{client})
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::match(['put', 'patch'], '/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    // Invoices (order: literal /create before /{invoice})
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'payment'])->name('invoices.payment');
    Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'status'])->name('invoices.status');
    Route::post('/invoices/{invoice}/email', [InvoiceController::class, 'email'])->name('invoices.email');
    Route::match(['put', 'patch'], '/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

    // Flight tickets (order: literal /create before /{flightTicket})
    Route::get('/tickets', [FlightTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [FlightTicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets/parse', [FlightTicketController::class, 'parse'])->name('tickets.parse');
    Route::post('/tickets', [FlightTicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{flightTicket}/edit', [FlightTicketController::class, 'edit'])->name('tickets.edit');
    Route::get('/tickets/{flightTicket}/pdf', [FlightTicketController::class, 'pdf'])->name('tickets.pdf');
    Route::get('/tickets/{flightTicket}/source', [FlightTicketController::class, 'source'])->name('tickets.source');
    Route::patch('/tickets/{flightTicket}/status', [FlightTicketController::class, 'status'])->name('tickets.status');
    Route::post('/tickets/{flightTicket}/email', [FlightTicketController::class, 'email'])->name('tickets.email');
    Route::match(['put', 'patch'], '/tickets/{flightTicket}', [FlightTicketController::class, 'update'])->name('tickets.update');
    Route::get('/tickets/{flightTicket}', [FlightTicketController::class, 'show'])->name('tickets.show');
    Route::delete('/tickets/{flightTicket}', [FlightTicketController::class, 'destroy'])->name('tickets.destroy');

    // Catalog management
    Route::resource('visas', VisaCountryController::class)
        ->parameters(['visas' => 'visa'])->except(['show']);
    Route::resource('packages', PackageController::class)->except(['show']);

    // Profile & settings (admin guard)
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'password'])->name('profile.password');
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-email', [SettingsController::class, 'test'])->name('settings.test');
});

require __DIR__.'/auth.php';
