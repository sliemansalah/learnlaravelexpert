<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            
            // بيانات الطالب
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->text('address')->nullable();
            $table->string('guardian_name');           // اسم ولي الأمر
            $table->string('guardian_phone');          // هاتف ولي الأمر
            $table->date('enrollment_date');           // تاريخ التسجيل
            $table->enum('status', ['active', 'inactive', 'graduated', 'transferred'])
                  ->default('active');
            
            // علاقة One-to-Many مع الفصل (الطالب ينتمي لفصل واحد)
            $table->foreignId('classroom_id')
                  ->constrained('classrooms')
                  ->onDelete('cascade');       // إذا حُذف الفصل، احذف الطلاب
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
