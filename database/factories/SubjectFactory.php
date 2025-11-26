<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    // قائمة المواد مع رموزها
    private static array $subjects = [
        ['name' => 'الرياضيات', 'code' => 'MATH'],
        ['name' => 'الفيزياء', 'code' => 'PHYS'],
        ['name' => 'الكيمياء', 'code' => 'CHEM'],
        ['name' => 'الأحياء', 'code' => 'BIO'],
        ['name' => 'اللغة العربية', 'code' => 'ARB'],
        ['name' => 'اللغة الإنجليزية', 'code' => 'ENG'],
        ['name' => 'التاريخ', 'code' => 'HIST'],
        ['name' => 'الجغرافيا', 'code' => 'GEO'],
        ['name' => 'الحاسب الآلي', 'code' => 'CS'],
        ['name' => 'التربية الإسلامية', 'code' => 'ISL'],
        ['name' => 'التربية البدنية', 'code' => 'PE'],
        ['name' => 'الفنون', 'code' => 'ART'],
    ];

    private static int $counter = 0;

    public function definition(): array
    {
        // اختيار مادة من القائمة بشكل دوري
        $subject = self::$subjects[self::$counter % count(self::$subjects)];
        self::$counter++;
        
        return [
            'name' => $subject['name'],
            'code' => $subject['code'] . fake()->unique()->numberBetween(100, 999),
            'description' => fake()->paragraph(),
            'credit_hours' => fake()->numberBetween(2, 4),
            'type' => fake()->randomElement(['mandatory', 'mandatory', 'elective']), // 66% mandatory
            'teacher_id' => Teacher::factory(),
        ];
    }

    /**
     * مادة إجبارية
     */
    public function mandatory(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'mandatory',
        ]);
    }

    /**
     * مادة اختيارية
     */
    public function elective(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'elective',
        ]);
    }

    /**
     * تعيين معلم معين
     */
    public function taughtBy(Teacher $teacher): static
    {
        return $this->state(fn (array $attributes) => [
            'teacher_id' => $teacher->id,
        ]);
    }
}
