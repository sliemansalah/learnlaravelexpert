<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    public function definition(): array
    {
        $gradeLevels = ['الصف الأول', 'الصف الثاني', 'الصف الثالث', 'الصف الرابع', 'الصف الخامس', 'الصف السادس'];
        $sections = ['أ', 'ب', 'ج', 'د'];
        
        $gradeLevel = fake()->randomElement($gradeLevels);
        $section = fake()->randomElement($sections);
        
        return [
            'name' => $gradeLevel . ' - ' . $section,
            'grade_level' => $gradeLevel,
            'capacity' => fake()->numberBetween(25, 35),
            'room_number' => fake()->numberBetween(100, 500),
            'teacher_id' => null, // سيتم تعيينه في الـ Seeder
        ];
    }

    /**
     * تعيين معلم للفصل
     */
    public function withTeacher(Teacher $teacher = null): static
    {
        return $this->state(fn (array $attributes) => [
            'teacher_id' => $teacher?->id ?? Teacher::factory(),
        ]);
    }
}
