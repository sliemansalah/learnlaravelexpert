<?php

namespace App\Providers;

use App\Http\Controllers\Api\ClassroomController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\Interfaces\ClassroomControllerInterface;
use App\Http\Controllers\Api\Interfaces\GradeControllerInterface;
use App\Http\Controllers\Api\Interfaces\StudentControllerInterface;
use App\Http\Controllers\Api\Interfaces\SubjectControllerInterface;
use App\Http\Controllers\Api\Interfaces\TeacherControllerInterface;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeacherController;
use Illuminate\Support\ServiceProvider;

class ControllerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register controller interfaces with their implementations
        $this->app->bind(TeacherControllerInterface::class, TeacherController::class);
        $this->app->bind(StudentControllerInterface::class, StudentController::class);
        $this->app->bind(ClassroomControllerInterface::class, ClassroomController::class);
        $this->app->bind(SubjectControllerInterface::class, SubjectController::class);
        $this->app->bind(GradeControllerInterface::class, GradeController::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
