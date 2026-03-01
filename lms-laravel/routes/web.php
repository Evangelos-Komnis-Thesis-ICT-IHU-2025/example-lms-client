<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Learner\LearnerController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/courses');

Route::prefix('admin')->group(function (): void {
    Route::get('/courses', [AdminCourseController::class, 'index'])->name('admin.courses.index');
    Route::post('/courses/import', [AdminCourseController::class, 'store'])->name('admin.courses.store');

    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');

    Route::get('/enrollments', [AdminEnrollmentController::class, 'index'])->name('admin.enrollments.index');
    Route::post('/enrollments', [AdminEnrollmentController::class, 'store'])->name('admin.enrollments.store');
});

Route::prefix('learner/users/{user}')->group(function (): void {
    Route::get('/courses', [LearnerController::class, 'courses'])->name('learner.courses');
    Route::post('/courses/{courseId}/launch', [LearnerController::class, 'launch'])->name('learner.launch');
    Route::get('/progress', [LearnerController::class, 'progress'])->name('learner.progress');
});
