<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\TeacherWebController;
use App\Http\Controllers\Web\StudentWebController;
use App\Http\Controllers\Web\ClassroomWebController;
use App\Http\Controllers\Web\SubjectWebController;
use App\Http\Controllers\Web\GradeWebController;

// الصفحة الرئيسية
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// مسارات المعلمين
Route::prefix('teachers')->name('teachers.')->group(function () {
    Route::get('/', [TeacherWebController::class, 'index'])->name('index');
    Route::get('/create', [TeacherWebController::class, 'create'])->name('create');
    Route::get('/{id}', [TeacherWebController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [TeacherWebController::class, 'edit'])->name('edit');
});

// مسارات الطلاب
Route::prefix('students')->name('students.')->group(function () {
    Route::get('/', [StudentWebController::class, 'index'])->name('index');
    Route::get('/create', [StudentWebController::class, 'create'])->name('create');
    Route::get('/{id}', [StudentWebController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [StudentWebController::class, 'edit'])->name('edit');
});

// مسارات الفصول
Route::prefix('classrooms')->name('classrooms.')->group(function () {
    Route::get('/', [ClassroomWebController::class, 'index'])->name('index');
    Route::get('/create', [ClassroomWebController::class, 'create'])->name('create');
    Route::get('/{id}', [ClassroomWebController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [ClassroomWebController::class, 'edit'])->name('edit');
});

// مسارات المواد
Route::prefix('subjects')->name('subjects.')->group(function () {
    Route::get('/', [SubjectWebController::class, 'index'])->name('index');
    Route::get('/create', [SubjectWebController::class, 'create'])->name('create');
    Route::get('/{id}', [SubjectWebController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [SubjectWebController::class, 'edit'])->name('edit');
});

// مسارات الدرجات
Route::prefix('grades')->name('grades.')->group(function () {
    Route::get('/', [GradeWebController::class, 'index'])->name('index');
    Route::get('/create', [GradeWebController::class, 'create'])->name('create');
    Route::get('/{id}', [GradeWebController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [GradeWebController::class, 'edit'])->name('edit');
});
