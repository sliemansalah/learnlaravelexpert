<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            
            // بيانات المادة
            $table->string('name');                   // اسم المادة
            $table->string('code')->unique();         // رمز المادة (MATH101)
            $table->text('description')->nullable();  // وصف المادة
            $table->integer('credit_hours');          // الساعات المعتمدة
            $table->enum('type', ['mandatory', 'elective'])
                  ->default('mandatory');             // إجباري/اختياري
            
            // علاقة One-to-Many مع المعلم (المادة يدرّسها معلم واحد)
            $table->foreignId('teacher_id')
                  ->constrained('teachers')
                  ->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
