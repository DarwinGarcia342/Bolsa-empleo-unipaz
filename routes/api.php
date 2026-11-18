<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\LawReferenceController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\StudentProfileController;
use App\Http\Middleware\EnsureActiveApiUser;
use App\Http\Middleware\EnsureApiStudent;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/auth/google', [AuthController::class, 'google'])->middleware('throttle:10,1');
    Route::post('/auth/register/company', [CompanyController::class, 'register'])->middleware('throttle:5,1');

    Route::get('/jobs', [JobController::class, 'index']);
    Route::get('/jobs/{jobPosting}', [JobController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me'])->middleware(EnsureActiveApiUser::class);

        Route::middleware([EnsureActiveApiUser::class, EnsureApiStudent::class])->group(function (): void {
            Route::get('/student/favorites', [JobController::class, 'favorites']);
            Route::get('/student/applications', [JobController::class, 'applications']);
            Route::get('/student/profile', [StudentProfileController::class, 'show']);
            Route::put('/jobs/{jobPosting}/favorite', [JobController::class, 'favorite']);
            Route::delete('/jobs/{jobPosting}/favorite', [JobController::class, 'unfavorite']);
            Route::post('/jobs/{jobPosting}/applications', [JobController::class, 'apply']);
            Route::put('/student/profile', [StudentProfileController::class, 'update']);
        });

        Route::middleware('api.role:company')->prefix('company')->group(function (): void {
            Route::get('/dashboard', [CompanyController::class, 'dashboard']);
            Route::get('/profile', [CompanyController::class, 'profile']);
            Route::put('/profile', [CompanyController::class, 'updateProfile']);
            Route::get('/reports', [CompanyController::class, 'reports']);
            Route::get('/jobs', [CompanyController::class, 'jobs']);
            Route::post('/jobs', [CompanyController::class, 'storeJob']);
            Route::get('/jobs/{jobPosting}', [CompanyController::class, 'showJob']);
            Route::put('/jobs/{jobPosting}', [CompanyController::class, 'updateJob']);
            Route::delete('/jobs/{jobPosting}', [CompanyController::class, 'closeJob']);
            Route::get('/applications', [CompanyController::class, 'applications']);
            Route::get('/applications/{application}', [CompanyController::class, 'showApplication']);
            Route::put('/applications/{application}/status', [CompanyController::class, 'updateApplication']);
        });

        Route::middleware('api.role:admin')->prefix('admin')->group(function (): void {
            Route::get('/dashboard', [AdminController::class, 'dashboard']);
            Route::get('/profile', [AdminController::class, 'profile']);
            Route::put('/profile', [AdminController::class, 'updateProfile']);
            Route::get('/reports', [AdminController::class, 'reports']);
            Route::get('/companies', [AdminController::class, 'companies']);
            Route::post('/companies/{company}/approve', [AdminController::class, 'approveCompany']);
            Route::post('/companies/{company}/reject', [AdminController::class, 'rejectCompany']);
            Route::delete('/companies/{company}', [AdminController::class, 'deleteCompany']);
            Route::get('/users', [AdminController::class, 'users']);
            Route::patch('/users/{user}/toggle', [AdminController::class, 'toggleUser']);
            Route::get('/jobs', [AdminController::class, 'jobs']);
            Route::get('/jobs/{jobPosting}', [AdminController::class, 'showJob']);
            Route::patch('/jobs/{jobPosting}/toggle', [AdminController::class, 'toggleJob']);
        });

        Route::middleware(EnsureActiveApiUser::class)->group(function (): void {
            Route::get('/notifications', [NotificationController::class, 'index']);
            Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
            Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
            Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
            Route::get('/laws', [LawReferenceController::class, 'index']);
            Route::get('/laws/{lawReference}', [LawReferenceController::class, 'show']);
        });
    });
});
