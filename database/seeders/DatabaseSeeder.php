<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('بدء إدخال البيانات التجريبية...');

        // 1. إنشاء المعلمين (10 معلمين)
        $this->command->info('إنشاء المعلمين...');
        $teachers = Teacher::factory(10)->create();

        // 2. إنشاء الفصول (6 فصول) وربطها بالمعلمين
        $this->command->info('إنشاء الفصول...');
        $classrooms = collect();

        foreach ($teachers->take(6) as $index => $teacher) {
            $classroom = Classroom::factory()->create([
                'teacher_id' => $teacher->id,
                'name' => 'الصف ' . ($index + 1) . ' - أ',
                'grade_level' => 'الصف ' . ($index + 1),
            ]);
            $classrooms->push($classroom);
        }

        // 3. إنشاء الطلاب (موزعين على الفصول)
        $this->command->info('إنشاء الطلاب...');
        $students = collect();

        foreach ($classrooms as $classroom) {
            $classroomStudents = Student::factory(fake()->numberBetween(15, 20))
                ->create(['classroom_id' => $classroom->id]);
            $students = $students->merge($classroomStudents);
        }

        // 4. إنشاء المواد وربطها بالمعلمين
        $this->command->info('إنشاء المواد...');

        $subjectsData = [
            ['name' => 'الرياضيات', 'code' => 'MATH101', 'credit_hours' => 4],
            ['name' => 'اللغة العربية', 'code' => 'ARB101', 'credit_hours' => 3],
            ['name' => 'اللغة الإنجليزية', 'code' => 'ENG101', 'credit_hours' => 3],
            ['name' => 'العلوم', 'code' => 'SCI101', 'credit_hours' => 4],
            ['name' => 'التربية الإسلامية', 'code' => 'ISL101', 'credit_hours' => 2],
            ['name' => 'الحاسب الآلي', 'code' => 'CS101', 'credit_hours' => 2],
            ['name' => 'التاريخ', 'code' => 'HIST101', 'credit_hours' => 2],
            ['name' => 'الجغرافيا', 'code' => 'GEO101', 'credit_hours' => 2],
        ];

        $subjects = collect();
        foreach ($subjectsData as $index => $subjectData) {
            $subject = Subject::create([
                'name' => $subjectData['name'],
                'code' => $subjectData['code'],
                'description' => 'وصف مادة ' . $subjectData['name'],
                'credit_hours' => $subjectData['credit_hours'],
                'type' => 'mandatory',
                'teacher_id' => $teachers[$index % $teachers->count()]->id,
            ]);
            $subjects->push($subject);
        }

        // 5. إنشاء الدرجات (ربط الطلاب بالمواد)
        $this->command->info('إنشاء الدرجات...');

        $semesters = ['الفصل الأول 2024', 'الفصل الثاني 2024'];
        $examTypes = ['midterm', 'final'];

        foreach ($students as $student) {
            $studentSubjects = $subjects->random(fake()->numberBetween(6, 8));

            foreach ($studentSubjects as $subject) {
                foreach ($semesters as $semester) {
                    foreach ($examTypes as $examType) {
                        Grade::create([
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'score' => fake()->randomFloat(2, 40, 100),
                            'semester' => $semester,
                            'exam_type' => $examType,
                            'notes' => fake()->optional(0.3)->sentence(),
                        ]);
                    }
                }
            }
        }

        // إحصائيات
        $this->command->info('');
        $this->command->info('تم إدخال البيانات بنجاح!');
        $this->command->table(
            ['الجدول', 'العدد'],
            [
                ['المعلمين', Teacher::count()],
                ['الفصول', Classroom::count()],
                ['الطلاب', Student::count()],
                ['المواد', Subject::count()],
                ['الدرجات', Grade::count()],
            ]
        );
    }
}
