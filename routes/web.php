<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\DashboardController;
use App\Http\Controllers\Library\BookController;
use App\Http\Controllers\Library\CategoryController;
use App\Http\Controllers\Library\RackController;
use App\Http\Controllers\Library\MemberController;
use App\Http\Controllers\Library\CirculationController;
use App\Http\Controllers\Library\VisitorController;
use App\Http\Controllers\Library\FineController;
use App\Http\Controllers\Library\ReportController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Books
Route::resource('books', BookController::class);
Route::get('api/books/search', [BookController::class, 'search'])->name('books.search');
Route::get('api/books/isbn/{isbn}', [BookController::class, 'findByIsbn'])->name('books.findByIsbn');
Route::get('api/search/global', [BookController::class, 'globalSearch'])->name('search.global');

// Categories
Route::resource('categories', CategoryController::class)->except(['show']);

// Racks
Route::resource('racks', RackController::class);

// Members
Route::get('members/card', [MemberController::class, 'card'])->name('members.card');
Route::resource('members', MemberController::class);
Route::get('api/members/{memberNumber}', [MemberController::class, 'findByNumber'])->name('members.findByNumber');
Route::post('members/{member}/extend', [MemberController::class, 'extend'])->name('members.extend');

// Circulation
Route::prefix('circulation')->name('circulation.')->group(function () {
    Route::get('borrow', [CirculationController::class, 'borrow'])->name('borrow');
    Route::post('borrow', [CirculationController::class, 'processBorrow'])->name('processBorrow');
    Route::get('return', [CirculationController::class, 'return'])->name('return');
    Route::post('return', [CirculationController::class, 'processReturn'])->name('processReturn');
    Route::get('active', [CirculationController::class, 'active'])->name('active');
    Route::get('history', [CirculationController::class, 'history'])->name('history');
});
Route::get('api/circulation/transaction/{isbn}', [CirculationController::class, 'findTransaction'])->name('circulation.findTransaction');

// Visitors
Route::get('visitors', [VisitorController::class, 'index'])->name('visitors.index');
Route::post('visitors/checkin', [VisitorController::class, 'manualCheckIn'])->name('visitors.checkin');
Route::post('visitors/{visitor}/checkout', [VisitorController::class, 'manualCheckOut'])->name('visitors.checkout');

// Kiosk Mode
Route::get('kiosk', [VisitorController::class, 'kiosk'])->name('kiosk');
Route::post('api/kiosk/checkin', [VisitorController::class, 'processCheckIn'])->name('kiosk.checkin');
Route::get('api/kiosk/count', [VisitorController::class, 'todayCount'])->name('kiosk.count');

// Fines
Route::get('fines', [FineController::class, 'index'])->name('fines.index');
Route::patch('fines/{fine}/pay', [FineController::class, 'markAsPaid'])->name('fines.mark-as-paid');
Route::delete('fines/{fine}', [FineController::class, 'destroy'])->name('fines.destroy');

// Reports
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('circulation', [ReportController::class, 'circulation'])->name('circulation');
    Route::get('visitors', [ReportController::class, 'visitors'])->name('visitors');
});

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;

// ... (existing imports)

// Auth
Route::get('/signin', function () {
    return view('pages.auth.signin', ['title' => 'Sign In']);
})->name('login');

Route::post('/signin', [AuthController::class, 'authenticate'])->name('login.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Sign Up']);
})->name('signup');

// Profile
// Profile
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/address', [ProfileController::class, 'updateAddress'])->name('profile.address.update');
});

