<?php

use App\Http\Controllers\Auth\CompanyAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Company\DashboardController as CompanyDashboard;
use App\Http\Controllers\Company\JobPostingController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\JobController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// --- Pagina principal publica ---
Route::get('/', [HomeController::class, 'index'])->name('home');

// --- Autenticacion ---
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login',   [LoginController::class, 'login'])->middleware('throttle:5,1'); // Máximo 5 intentos por minuto

    Route::get('/register/empresa',   [CompanyAuthController::class, 'showRegister'])->name('company.register');
    Route::post('/register/empresa',  [CompanyAuthController::class, 'register'])->name('company.register.store');

    Route::get('/auth/google',          [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
    ->middleware('auth')
    ->name('notifications.read');

Route::get('/hojas-de-vida/{user}', [StudentDashboard::class, 'resume'])
    ->middleware('auth')
    ->name('resumes.show');

// --- Panel de ADMINISTRADOR ---
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin', 'prevent-back'])->group(function () {
    Route::get('/dashboard',                [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/perfil',                   [AdminDashboard::class, 'profile'])->name('profile');
    Route::put('/perfil',                   [AdminDashboard::class, 'updateProfile'])->name('profile.update');
    Route::get('/empresas',                 [AdminDashboard::class, 'companies'])->name('companies');
    Route::post('/empresas/{company}/aprobar', [AdminDashboard::class, 'approveCompany'])->name('companies.approve');
    Route::post('/empresas/{company}/rechazar', [AdminDashboard::class, 'rejectCompany'])->name('companies.reject');
    Route::delete('/empresas/{company}', [AdminDashboard::class, 'deleteCompany'])->name('companies.delete');
    Route::get('/usuarios',                 [AdminDashboard::class, 'users'])->name('users');
    Route::post('/usuarios/{user}/toggle',  [AdminDashboard::class, 'toggleUser'])->name('users.toggle');
    Route::get('/vacantes',                  [AdminDashboard::class, 'vacancies'])->name('vacancies');
    Route::get('/vacantes/{jobPosting}',     [AdminDashboard::class, 'showVacancy'])->name('vacancies.show');
    Route::post('/vacantes/{jobPosting}/toggle', [AdminDashboard::class, 'toggleVacancy'])->name('vacancies.toggle');
    Route::get('/reportes',                 [AdminDashboard::class, 'reports'])->name('reports');
});

// --- Panel de EMPRESA ---
Route::prefix('empresa')->name('company.')->middleware(['auth', 'role:company', 'prevent-back'])->group(function () {
    Route::get('/dashboard',   [CompanyDashboard::class, 'index'])->name('dashboard');
    Route::get('/perfil',      [CompanyDashboard::class, 'profile'])->name('profile');
    Route::put('/perfil',      [CompanyDashboard::class, 'updateProfile'])->name('profile.update');
    Route::get('/postulaciones', [CompanyDashboard::class, 'applications'])->name('applications');
    Route::put('/postulaciones/{application}', [CompanyDashboard::class, 'updateApplicationStatus'])
        ->name('applications.update');
    Route::get('/reportes', [CompanyDashboard::class, 'reports'])->name('reports');

    Route::middleware('company.approved')->group(function () {
        Route::post('/vacantes/{jobPosting}/toggle', [JobPostingController::class, 'toggleStatus'])
            ->name('jobs.toggle');

        // CRUD de vacantes
        Route::resource('vacantes', JobPostingController::class, [
            'names'  => [
                'index'   => 'jobs.index',
                'create'  => 'jobs.create',
                'store'   => 'jobs.store',
                'show'    => 'jobs.show',
                'edit'    => 'jobs.edit',
                'update'  => 'jobs.update',
                'destroy' => 'jobs.destroy',
            ],
            'parameters' => ['vacantes' => 'jobPosting'],
        ]);
    });
});

// --- Panel de ESTUDIANTE ---
Route::prefix('estudiante')->name('student.')->middleware(['auth', 'role:student', 'prevent-back'])->group(function () {
    Route::get('/dashboard',     [StudentDashboard::class, 'index'])->name('dashboard');
    Route::get('/perfil',        [StudentDashboard::class, 'profile'])->name('profile');
    Route::put('/perfil',        [StudentDashboard::class, 'updateProfile'])->name('profile.update');
    Route::get('/perfil/hoja-de-vida', [StudentDashboard::class, 'myResume'])->name('profile.resume');
    Route::get('/postulaciones', [StudentDashboard::class, 'myApplications'])->name('applications');
    Route::get('/favoritos',     [JobController::class, 'favorites'])->name('jobs.favorites');

    // Vacantes
    Route::get('/vacantes',              [JobController::class, 'index'])->name('jobs');
    Route::get('/vacantes/{jobPosting}', [JobController::class, 'show'])->name('jobs.show');
    Route::post('/vacantes/{jobPosting}/favorito', [JobController::class, 'toggleFavorite'])->name('jobs.favorite');
    Route::post('/vacantes/{jobPosting}/postular', [JobController::class, 'apply'])->name('jobs.apply');
});
