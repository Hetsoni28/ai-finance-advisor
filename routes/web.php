<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 📥 CONTROLLER REGISTRY
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\IncomeController;
use App\Http\Controllers\User\ExpenseController;
use App\Http\Controllers\User\FamilyController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ReportController;
use App\Http\Controllers\User\AiChatController;
use App\Http\Controllers\User\NotificationController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SecurityController as AdminSecurityController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;

/*
|--------------------------------------------------------------------------
| 🌍 PUBLIC PERIMETER
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::view('/features', 'pages.features')->name('features');
Route::view('/pricing', 'pages.pricing')->name('pricing');
Route::view('/about', 'pages.about')->name('about');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/contact', 'pages.contact')->name('contact');

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('contact.store');

/*
|--------------------------------------------------------------------------
| 🔐 AUTHENTICATION GATEWAY
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
    
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');

    Route::get('/register', [AuthController::class, 'registerPage'])->name('register');
    
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

/*
|--------------------------------------------------------------------------
| 🔓 HYBRID ROUTES (Magic Links & Webhooks)
|--------------------------------------------------------------------------
| Routes that require controller-level intelligence rather than strict 
| route-level auth blocks.
*/

Route::prefix('user')->name('user.')->group(function () {
    // 🔥 BEAST MODE: 1-Click Magic Link Entry Point
    Route::get('families/{family}/accept/{token}', [FamilyController::class, 'acceptInvite'])
        ->name('families.accept');
});

/*
|--------------------------------------------------------------------------
| 🛡️ SECURE INTERNAL NETWORK (AUTHENTICATED)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Global Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |========================================================================
    | 👤 USER HUB
    |========================================================================
    */
    Route::prefix('user')->name('user.')->group(function () {

        // --- DASHBOARD ---
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // --- EXPENSES ---
        Route::get('/expenses/export/pdf', [ExpenseController::class, 'exportPdf'])->name('expenses.export.pdf');
        Route::resource('expenses', ExpenseController::class);

        // --- INCOMES ---
        Route::get('/incomes/export/pdf', [IncomeController::class, 'exportPdf'])->name('incomes.export.pdf');
        Route::resource('incomes', IncomeController::class)->except('show');

        // --- COLLABORATIVE WORKSPACES (FAMILIES) ---
        Route::resource('families', FamilyController::class)->only(['index', 'create', 'store', 'show']);

        // Access Management UI
        Route::get('families/{family}/access', [FamilyController::class, 'accessManagement'])->name('families.access');

        // Secure Handshakes (Invites)
        Route::post('families/{family}/invite', [FamilyController::class, 'invite'])
            ->middleware('throttle:5,1')
            ->name('families.invite');

        // IAM & Access Revocation Routes
        Route::delete('families/{family}/members/{member}', [FamilyController::class, 'removeMember'])
            ->name('families.removeMember');

        Route::delete('families/{family}/invites/{invite}', [FamilyController::class, 'destroyInvite'])
            ->name('families.invites.destroy');

        Route::delete('families/{family}/invites-bulk', [FamilyController::class, 'bulkDestroyInvites'])
            ->middleware('throttle:5,1') 
            ->name('families.invites.bulkDestroy');

        // --- FINANCE AI ENGINE ---
        Route::prefix('ai')->name('ai.')->group(function () {
            Route::get('/chat', [AiChatController::class, 'index'])->name('chat');
            Route::post('/chat/send', [AiChatController::class, 'sendMessage'])
                ->middleware('throttle:30,1')
                ->name('chat.send');
        });

        // --- NOTIFICATIONS ---
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

        // --- REPORTS ---
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        // --- PROFILE & BILLING ---
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
            
            // 🚨 RESTful Upgrade: Updates use PATCH
            Route::patch('/update', [ProfileController::class, 'update'])->name('update');

            Route::get('/password', [ProfileController::class, 'passwordForm'])->name('password.form');
            Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password.update');

            Route::view('/subscription', 'user.profile.subscription')->name('subscription');
        });
    });

    /*
    |========================================================================
    | 👑 MASTER ADMIN NODE
    |========================================================================
    */
    Route::prefix('admin')
        ->middleware('admin')
        ->name('admin.')
        ->group(function () {

        // --- SYSTEM DASHBOARD ---
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // --- USER MANAGEMENT ---
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/block', [AdminUserController::class, 'block'])->name('users.block');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // --- SECURITY LOGS ---
        Route::get('/activities', [AdminSecurityController::class, 'index'])->name('activities.index');

        // --- GLOBAL REPORTS ---
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [AdminReportController::class, 'exportPdf'])->name('reports.pdf');
        
    });

});