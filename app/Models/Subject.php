<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'credit_hours',
        'type',
        'teacher_id',
    ];

    /**
     * علاقة One-to-Many (العكس): المادة يدرّسها معلم واحد
     * Subject belongsTo Teacher
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * علاقة غير مباشرة: الحصول على الفصل عبر المعلم
     * Subject hasOneThrough Classroom
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'teacher_id', 'teacher_id');
    }

    /**
     * علاقة Many-to-Many: المادة يدرسها عدة طلاب
     * Subject belongsToMany Students (عبر جدول grades)
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'grades')
                    ->withPivot('score', 'semester', 'exam_type', 'notes')
                    ->withTimestamps();
    }

    /**
     * علاقة One-to-Many: درجات المادة
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    // ============ Helper Methods ============

    /**
     * متوسط درجات المادة
     */
    public function averageScore(): float
    {
        $average = $this->grades()->avg('score');
        return $average ? round($average, 2) : 0;
    }

    /**
     * عدد الطلاب المسجلين في المادة
     */
    public function enrolledStudentsCount(): int
    {
        return $this->students()->distinct()->count();
    }

    /**
     * أعلى درجة في المادة
     */
    public function highestScore(): float
    {
        return $this->grades()->max('score') ?? 0;
    }

    /**
     * أدنى درجة في المادة
     */
    public function lowestScore(): float
    {
        return $this->grades()->min('score') ?? 0;
    }
}
