<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            
            // بيانات الفصل
            $table->string('name');                    // اسم الفصل (مثل: 3A)
            $table->string('grade_level');             // المرحلة (أول، ثاني، ثالث)
            $table->integer('capacity')->default(30); // السعة القصوى
            $table->string('room_number')->nullable(); // رقم الغرفة
            
            // علاقة One-to-One مع المعلم (رئيس الفصل)
            $table->foreignId('teacher_id')
                  ->nullable()
                  ->constrained('teachers')      // يشير لجدول teachers
                  ->onDelete('set null');        // إذا حُذف المعلم، اجعل القيمة null
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
