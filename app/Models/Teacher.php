<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    // الحقول المسموح بتعبئتها
    protected $fillable = [
        'name',
        'email',
        'phone',
        'specialization',
        'hire_date',
        'salary',
        'status',
    ];

    // تحويل أنواع البيانات
    protected $casts = [
        'hire_date' => 'date:Y-m-d',
        'salary' => 'decimal:2',
    ];

    /**
     * علاقة One-to-One: المعلم له فصل واحد (كرئيس فصل)
     * Teacher hasOne Classroom
     */
    public function classroom(): HasOne
    {
        return $this->hasOne(Classroom::class);
    }

    /**
     * علاقة One-to-Many: المعلم يدرّس عدة مواد
     * Teacher hasMany Subjects
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    // ============ Helper Methods ============

    /**
     * هل المعلم نشط؟
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * عدد المواد التي يدرّسها
     */
    public function subjectsCount(): int
    {
        return $this->subjects()->count();
    }
}
