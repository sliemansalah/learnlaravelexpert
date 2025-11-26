<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    /**
     * تعريف القالب الافتراضي للمعلم
     */
    public function definition(): array
    {
        $specializations = ['رياضيات', 'فيزياء', 'كيمياء', 'أحياء', 'لغة عربية', 'لغة إنجليزية', 'تاريخ', 'جغرافيا', 'حاسب آلي'];
        
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'specialization' => fake()->randomElement($specializations),
            'hire_date' => fake()->dateTimeBetween('-10 years', '-1 year'),
            'salary' => fake()->randomFloat(2, 3000, 15000),
            'status' => fake()->randomElement(['active', 'active', 'active', 'inactive', 'on_leave']), // 60% active
        ];
    }

    /**
     * حالة: معلم نشط
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * حالة: معلم براتب مرتفع
     */
    public function highSalary(): static
    {
        return $this->state(fn (array $attributes) => [
            'salary' => fake()->randomFloat(2, 12000, 20000),
        ]);
    }
}
