<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'grade_level',
        'capacity',
        'room_number',
        'teacher_id',
    ];

    /**
     * علاقة One-to-One (العكس): الفصل ينتمي لمعلم واحد
     * Classroom belongsTo Teacher
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * علاقة One-to-Many: الفصل فيه عدة طلاب
     * Classroom hasMany Students
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    // ============ Helper Methods ============

    /**
     * عدد الطلاب في الفصل
     */
    public function studentsCount(): int
    {
        return $this->students()->count();
    }

    /**
     * هل الفصل ممتلئ؟
     */
    public function isFull(): bool
    {
        return $this->studentsCount() >= $this->capacity;
    }

    /**
     * المقاعد المتاحة
     */
    public function availableSeats(): int
    {
        return $this->capacity - $this->studentsCount();
    }
}
