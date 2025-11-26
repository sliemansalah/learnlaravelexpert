<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'score',
        'semester',
        'exam_type',
        'notes',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    /**
     * علاقة مع الطالب
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * علاقة مع المادة
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    // ============ Helper Methods ============

    /**
     * الحصول على التقدير بالحروف
     */
    public function getLetterGrade(): string
    {
        return match(true) {
            $this->score >= 90 => 'A',
            $this->score >= 80 => 'B',
            $this->score >= 70 => 'C',
            $this->score >= 60 => 'D',
            default => 'F',
        };
    }

    /**
     * هل الطالب ناجح؟
     */
    public function isPassing(): bool
    {
        return $this->score >= 60;
    }
}
