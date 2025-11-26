<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);
        
        return [
            'name' => fake()->name($gender),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'birth_date' => fake()->dateTimeBetween('-18 years', '-6 years'),
            'gender' => $gender,
            'address' => fake()->address(),
            'guardian_name' => fake()->name(),
            'guardian_phone' => fake()->phoneNumber(),
            'enrollment_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'status' => fake()->randomElement(['active', 'active', 'active', 'inactive', 'graduated', 'transferred']),
            'classroom_id' => Classroom::factory(),
        ];
    }

    /**
     * طالب ذكر
     */
    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'male',
            'name' => fake()->name('male'),
        ]);
    }

    /**
     * طالبة أنثى
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'female',
            'name' => fake()->name('female'),
        ]);
    }

    /**
     * طالب نشط
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * تعيين فصل معين
     */
    public function inClassroom(Classroom $classroom): static
    {
        return $this->state(fn (array $attributes) => [
            'classroom_id' => $classroom->id,
        ]);
    }
}
