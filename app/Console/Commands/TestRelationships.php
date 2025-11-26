<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Grade;

class TestRelationships extends Command
{
    protected $signature = 'app:test-relationships';
    protected $description = 'Test all relationships';

    public function handle()
    {
        $this->info('===========================================');
        $this->info('   Testing School Management Relationships');
        $this->info('===========================================');
        $this->newLine();

        $this->testOneToOne();
        $this->testBelongsTo();
        $this->testOneToMany();
        $this->testTeacherSubjects();
        $this->testManyToMany();
        $this->testPivotData();
        $this->testEagerLoading();
        $this->testHelperMethods();
        $this->testQueryBuilder();

        $this->newLine();
        $this->info('===========================================');
        $this->info('   All relationships tested successfully!');
        $this->info('===========================================');
    }

    private function testOneToOne()
    {
        $this->warn('1. One-to-One (hasOne): Teacher -> Classroom');
        $this->line('-------------------------------------------');
        $teacher = Teacher::has('classroom')->first();
        if ($teacher) {
            $this->info("Teacher: {$teacher->name}");
            $this->info("Classroom: {$teacher->classroom->name}");
            $this->info("Grade Level: {$teacher->classroom->grade_level}");
            $this->newLine();
            $this->comment('Code: $teacher->classroom->name;');
        }
        $this->newLine();
    }

    private function testBelongsTo()
    {
        $this->warn('2. belongsTo: Classroom -> Teacher');
        $this->line('-------------------------------------------');
        $classroom = Classroom::whereNotNull('teacher_id')->first();
        if ($classroom) {
            $this->info("Classroom: {$classroom->name}");
            $this->info("Teacher: {$classroom->teacher->name}");
            $this->info("Specialization: {$classroom->teacher->specialization}");
            $this->newLine();
            $this->comment('Code: $classroom->teacher->name;');
        }
        $this->newLine();
    }

    private function testOneToMany()
    {
        $this->warn('3. One-to-Many (hasMany): Classroom -> Students');
        $this->line('-------------------------------------------');
        $classroom = Classroom::withCount('students')->first();
        if ($classroom) {
            $this->info("Classroom: {$classroom->name}");
            $this->info("Students count: {$classroom->students_count}");
            $this->newLine();
            $this->info('First 5 students:');
            $students = $classroom->students()->take(5)->get();
            $tableData = $students->map(fn($s) => [$s->id, $s->name, $s->gender, $s->status])->toArray();
            $this->table(['ID', 'Name', 'Gender', 'Status'], $tableData);
            $this->newLine();
            $this->comment('Code: $classroom->students()->get();');
        }
        $this->newLine();
    }

    private function testTeacherSubjects()
    {
        $this->warn('4. One-to-Many: Teacher -> Subjects');
        $this->line('-------------------------------------------');
        $teacher = Teacher::has('subjects')->withCount('subjects')->first();
        if ($teacher) {
            $this->info("Teacher: {$teacher->name}");
            $this->info("Subjects count: {$teacher->subjects_count}");
            $this->newLine();
            $this->info('Subjects:');
            $subjects = $teacher->subjects;
            $tableData = $subjects->map(fn($s) => [$s->code, $s->name, $s->credit_hours, $s->type])->toArray();
            $this->table(['Code', 'Subject', 'Hours', 'Type'], $tableData);
            $this->newLine();
            $this->comment('Code: $teacher->subjects;');
        }
        $this->newLine();
    }

    private function testManyToMany()
    {
        $this->warn('5. Many-to-Many: Student <-> Subjects');
        $this->line('-------------------------------------------');
        $student = Student::has('subjects')->first();
        if ($student) {
            $this->info("Student: {$student->name}");
            $this->info('Subjects enrolled: ' . $student->subjects()->distinct()->count());
            $this->newLine();
            $this->info('Subjects (distinct):');
            $subjects = $student->subjects()->distinct()->take(5)->get();
            $tableData = $subjects->map(fn($s) => [$s->code, $s->name])->toArray();
            $this->table(['Code', 'Subject'], $tableData);
            $this->newLine();
            $this->comment('Code: $student->subjects()->get();');
        }
        $this->newLine();
    }

    private function testPivotData()
    {
        $this->warn('6. Pivot Data Access (Grades)');
        $this->line('-------------------------------------------');
        $student = Student::has('subjects')->first();
        if ($student) {
            $this->info("Student: {$student->name}");
            $this->newLine();
            $this->info('Student grades (first 5):');
            $subjects = $student->subjects()->take(5)->get();
            $tableData = $subjects->map(fn($s) => [$s->name, $s->pivot->score, $s->pivot->semester, $s->pivot->exam_type])->toArray();
            $this->table(['Subject', 'Score', 'Semester', 'Exam Type'], $tableData);
            $this->newLine();
            $this->comment('Code: $subject->pivot->score;');
        }
        $this->newLine();
    }

    private function testEagerLoading()
    {
        $this->warn('7. Eager Loading');
        $this->line('-------------------------------------------');
        $this->info('Loading classrooms with teachers and students:');
        $classrooms = Classroom::with(['teacher', 'students'])->take(3)->get();
        $tableData = $classrooms->map(fn($c) => [$c->name, $c->teacher?->name ?? 'N/A', $c->students->count()])->toArray();
        $this->table(['Classroom', 'Teacher', 'Students'], $tableData);
        $this->newLine();
        $this->comment('Code: Classroom::with(["teacher", "students"])->get();');
        $this->newLine();
        $this->info('Benefits:');
        $this->line('- Prevents N+1 Query problem');
        $this->line('- Loads all relations efficiently');
        $this->newLine();
    }

    private function testHelperMethods()
    {
        $this->warn('8. Helper Methods');
        $this->line('-------------------------------------------');
        $student = Student::has('grades')->first();
        if ($student) {
            $this->info("Student: {$student->name}");
            $this->info('GPA: ' . $student->calculateGPA());
            $this->info('Age: ' . $student->age() . ' years');
            $this->newLine();
        }
        $subject = Subject::has('grades')->first();
        if ($subject) {
            $this->info("Subject: {$subject->name}");
            $this->info('Average Score: ' . $subject->averageScore());
            $this->info('Highest Score: ' . $subject->highestScore());
            $this->info('Lowest Score: ' . $subject->lowestScore());
            $this->info('Enrolled Students: ' . $subject->enrolledStudentsCount());
            $this->newLine();
        }
        $classroom = Classroom::has('students')->first();
        if ($classroom) {
            $this->info("Classroom: {$classroom->name}");
            $this->info('Students: ' . $classroom->studentsCount());
            $this->info('Capacity: ' . $classroom->capacity);
            $this->info('Available Seats: ' . $classroom->availableSeats());
            $this->info('Is Full: ' . ($classroom->isFull() ? 'Yes' : 'No'));
            $this->newLine();
        }
        $grade = Grade::first();
        if ($grade) {
            $this->info('Score: ' . $grade->score);
            $this->info('Letter Grade: ' . $grade->getLetterGrade());
            $this->info('Passing: ' . ($grade->isPassing() ? 'Yes' : 'No'));
        }
        $this->newLine();
    }

    private function testQueryBuilder()
    {
        $this->warn('9. Query Builder with Relations');
        $this->line('-------------------------------------------');
        $this->info('Students with at least one grade >= 90:');
        $topStudents = Student::whereHas('grades', fn($q) => $q->where('score', '>=', 90))->take(5)->get();
        $tableData = $topStudents->map(fn($s) => [$s->name, $s->grades()->max('score')])->toArray();
        $this->table(['Student', 'Max Score'], $tableData);
        $this->newLine();
        $this->comment('Code: Student::whereHas("grades", fn($q) => $q->where("score", ">=", 90))->get();');
        $this->newLine();
    }
}