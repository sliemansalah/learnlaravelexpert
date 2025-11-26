<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // الجدول الوسيط (Pivot Table) لعلاقة Many-to-Many
        // بين الطلاب والمواد
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            
            // المفاتيح الأجنبية للعلاقة
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade');
            
            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->onDelete('cascade');
            
            // بيانات إضافية في الجدول الوسيط
            $table->decimal('score', 5, 2)->default(0);      // الدرجة (من 100)
            $table->string('semester');                       // الفصل الدراسي
            $table->enum('exam_type', ['midterm', 'final', 'quiz', 'assignment'])
                  ->default('final');                         // نوع الاختبار
            $table->text('notes')->nullable();                // ملاحظات
            
            $table->timestamps();
            
            // منع تكرار نفس الطالب والمادة والفصل ونوع الاختبار
            $table->unique(['student_id', 'subject_id', 'semester', 'exam_type'], 'unique_grade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
