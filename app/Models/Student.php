<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'birth_date',
        'gender',
        'address',
        'guardian_name',
        'guardian_phone',
        'enrollment_date',
        'status',
        'classroom_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'enrollment_date' => 'date',
    ];

    /**
     * علاقة One-to-Many (العكس): الطالب ينتمي لفصل واحد
     * Student belongsTo Classroom
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * علاقة Many-to-Many: الطالب يدرس عدة مواد
     * Student belongsToMany Subjects (عبر جدول grades)
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'grades')
                    ->withPivot('score', 'semester', 'exam_type', 'notes')
                    ->withTimestamps();
    }

    /**
     * علاقة One-to-Many: درجات الطالب
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    // ============ Helper Methods ============

    /**
     * حساب المعدل العام للطالب
     */
    public function calculateGPA(): float
    {
        $average = $this->grades()->avg('score');
        return $average ? round($average, 2) : 0;
    }

    /**
     * الحصول على درجة مادة معينة
     */
    public function getSubjectGrade(int $subjectId, string $semester): ?float
    {
        $grade = $this->grades()
                      ->where('subject_id', $subjectId)
                      ->where('semester', $semester)
                      ->first();
        
        return $grade?->score;
    }

    /**
     * حساب العمر
     */
    public function age(): int
    {
        return $this->birth_date->age;
    }
}
