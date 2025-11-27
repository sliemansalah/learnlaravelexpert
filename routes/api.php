<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ClassroomController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\GradeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============ Teachers Routes ============
Route::prefix('teachers')->group(function () {
    Route::get('/', [TeacherController::class, 'index']); // GET /api/teachers
    Route::post('/', [TeacherController::class, 'store']); // POST /api/teachers
    Route::get('/search', [TeacherController::class, 'search']); // GET /api/teachers/search
    Route::get('/{id}', [TeacherController::class, 'show']); // GET /api/teachers/{id}
    Route::put('/{id}', [TeacherController::class, 'update']); // PUT /api/teachers/{id}
    Route::patch('/{id}', [TeacherController::class, 'update']); // PATCH /api/teachers/{id}
    Route::delete('/{id}', [TeacherController::class, 'destroy']); // DELETE /api/teachers/{id}
    Route::get('/{id}/subjects', [TeacherController::class, 'subjects']); // GET /api/teachers/{id}/subjects
});

// ============ Students Routes ============
Route::prefix('students')->group(function () {
    Route::get('/', [StudentController::class, 'index']); // GET /api/students
    Route::post('/', [StudentController::class, 'store']); // POST /api/students
    Route::get('/search', [StudentController::class, 'search']); // GET /api/students/search
    Route::get('/{id}', [StudentController::class, 'show']); // GET /api/students/{id}
    Route::put('/{id}', [StudentController::class, 'update']); // PUT /api/students/{id}
    Route::patch('/{id}', [StudentController::class, 'update']); // PATCH /api/students/{id}
    Route::delete('/{id}', [StudentController::class, 'destroy']); // DELETE /api/students/{id}
    Route::get('/{id}/grades', [StudentController::class, 'grades']); // GET /api/students/{id}/grades
    Route::post('/{id}/transfer', [StudentController::class, 'transfer']); // POST /api/students/{id}/transfer
});

// ============ Classrooms Routes ============
Route::prefix('classrooms')->group(function () {
    Route::get('/', [ClassroomController::class, 'index']); // GET /api/classrooms
    Route::post('/', [ClassroomController::class, 'store']); // POST /api/classrooms
    Route::get('/search', [ClassroomController::class, 'search']); // GET /api/classrooms/search
    Route::get('/{id}', [ClassroomController::class, 'show']); // GET /api/classrooms/{id}
    Route::put('/{id}', [ClassroomController::class, 'update']); // PUT /api/classrooms/{id}
    Route::patch('/{id}', [ClassroomController::class, 'update']); // PATCH /api/classrooms/{id}
    Route::delete('/{id}', [ClassroomController::class, 'destroy']); // DELETE /api/classrooms/{id}
    Route::get('/{id}/students', [ClassroomController::class, 'students']); // GET /api/classrooms/{id}/students
    Route::post('/{id}/assign-teacher', [ClassroomController::class, 'assignTeacher']); // POST /api/classrooms/{id}/assign-teacher
});

// ============ Subjects Routes ============
Route::prefix('subjects')->group(function () {
    Route::get('/', [SubjectController::class, 'index']); // GET /api/subjects
    Route::post('/', [SubjectController::class, 'store']); // POST /api/subjects
    Route::get('/search', [SubjectController::class, 'search']); // GET /api/subjects/search
    Route::get('/{id}', [SubjectController::class, 'show']); // GET /api/subjects/{id}
    Route::put('/{id}', [SubjectController::class, 'update']); // PUT /api/subjects/{id}
    Route::patch('/{id}', [SubjectController::class, 'update']); // PATCH /api/subjects/{id}
    Route::delete('/{id}', [SubjectController::class, 'destroy']); // DELETE /api/subjects/{id}
    Route::get('/{id}/students', [SubjectController::class, 'students']); // GET /api/subjects/{id}/students
    Route::get('/{id}/grades', [SubjectController::class, 'grades']); // GET /api/subjects/{id}/grades
    Route::post('/{id}/assign-teacher', [SubjectController::class, 'assignTeacher']); // POST /api/subjects/{id}/assign-teacher
});

// ============ Grades Routes ============
Route::prefix('grades')->group(function () {
    Route::get('/', [GradeController::class, 'index']); // GET /api/grades
    Route::post('/', [GradeController::class, 'store']); // POST /api/grades
    Route::get('/search', [GradeController::class, 'search']); // GET /api/grades/search
    Route::get('/student/{student_id}', [GradeController::class, 'studentGrades']); // GET /api/grades/student/{student_id}
    Route::get('/subject/{subject_id}', [GradeController::class, 'subjectGrades']); // GET /api/grades/ /{subject_id}
    Route::get('/report/semester/{semester}', [GradeController::class, 'semesterReport']); // GET /api/grades/report/semester/{semester}
    Route::get('/{id}', [GradeController::class, 'show']); // GET /api/grades/{id}
    Route::put('/{id}', [GradeController::class, 'update']); // PUT /api/grades/{id}
    Route::patch('/{id}', [GradeController::class, 'update']); // PATCH /api/grades/{id}
    Route::delete('/{id}', [GradeController::class, 'destroy']); // DELETE /api/grades/{id}
});

// ============ Health Check Route ============
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working!',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});
