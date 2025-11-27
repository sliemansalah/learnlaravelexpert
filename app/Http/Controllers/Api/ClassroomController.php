<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Interfaces\ClassroomControllerInterface;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassroomController extends Controller implements ClassroomControllerInterface
{
    /**
     * عرض قائمة جميع الفصول
     * GET /api/classrooms
     */
    public function index(): JsonResponse
    {
        $classrooms = Classroom::with(['teacher', 'students'])
            ->withCount('students')
            ->get()
            ->map(function ($classroom) {
                $classroom->is_full = $classroom->isFull();
                $classroom->available_seats = $classroom->availableSeats();
                return $classroom;
            });

        return response()->json([
            'success' => true,
            'data' => $classrooms,
        ]);
    }

    /**
     * إنشاء فصل جديد
     * POST /api/classrooms
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'grade_level' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'room_number' => 'required|string|max:20',
            'teacher_id' => 'nullable|exists:teachers,id|unique:classrooms,teacher_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $classroom = Classroom::create($request->all());
        $classroom->load('teacher');

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الفصل بنجاح',
            'data' => $classroom,
        ], 201);
    }

    /**
     * عرض فصل محدد
     * GET /api/classrooms/{id}
     */
    public function show(string $id): JsonResponse
    {
        $classroom = Classroom::with(['teacher', 'students'])->find($id);

        if (!$classroom) {
            return response()->json([
                'success' => false,
                'message' => 'الفصل غير موجود',
            ], 404);
        }

        $classroom->is_full = $classroom->isFull();
        $classroom->available_seats = $classroom->availableSeats();
        $classroom->students_count = $classroom->studentsCount();

        return response()->json([
            'success' => true,
            'data' => $classroom,
        ]);
    }

    /**
     * تحديث فصل موجود
     * PUT/PATCH /api/classrooms/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $classroom = Classroom::find($id);

        if (!$classroom) {
            return response()->json([
                'success' => false,
                'message' => 'الفصل غير موجود',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            'grade_level' => 'sometimes|required|string|max:50',
            'capacity' => 'sometimes|required|integer|min:1',
            'room_number' => 'sometimes|required|string|max:20',
            'teacher_id' => 'nullable|exists:teachers,id|unique:classrooms,teacher_id,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $classroom->update($request->all());
        $classroom->load('teacher');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الفصل بنجاح',
            'data' => $classroom,
        ]);
    }

    /**
     * حذف فصل
     * DELETE /api/classrooms/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $classroom = Classroom::find($id);

        if (!$classroom) {
            return response()->json([
                'success' => false,
                'message' => 'الفصل غير موجود',
            ], 404);
        }

        // التحقق من عدم وجود طلاب في الفصل
        if ($classroom->studentsCount() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الفصل لأنه يحتوي على طلاب',
            ], 400);
        }

        $classroom->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الفصل بنجاح',
        ]);
    }

    /**
     * الحصول على طلاب الفصل
     * GET /api/classrooms/{id}/students
     */
    public function students(string $id): JsonResponse
    {
        $classroom = Classroom::with(['students.grades'])->find($id);

        if (!$classroom) {
            return response()->json([
                'success' => false,
                'message' => 'الفصل غير موجود',
            ], 404);
        }

        $students = $classroom->students->map(function ($student) {
            $student->gpa = $student->calculateGPA();
            return $student;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'classroom' => $classroom->only(['id', 'name', 'grade_level']),
                'students_count' => $classroom->studentsCount(),
                'students' => $students,
            ],
        ]);
    }

    /**
     * البحث عن الفصول
     * GET /api/classrooms/search?query=name&grade_level=1
     */
    public function search(Request $request): JsonResponse
    {
        $query = Classroom::query();

        if ($request->has('query')) {
            $searchTerm = $request->query('query');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('room_number', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->has('grade_level')) {
            $query->where('grade_level', $request->query('grade_level'));
        }

        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->query('teacher_id'));
        }

        $classrooms = $query->with(['teacher', 'students'])
            ->withCount('students')
            ->get()
            ->map(function ($classroom) {
                $classroom->is_full = $classroom->isFull();
                $classroom->available_seats = $classroom->availableSeats();
                return $classroom;
            });

        return response()->json([
            'success' => true,
            'data' => $classrooms,
        ]);
    }

    /**
     * تعيين معلم للفصل
     * POST /api/classrooms/{id}/assign-teacher
     */
    public function assignTeacher(Request $request, string $id): JsonResponse
    {
        $classroom = Classroom::find($id);

        if (!$classroom) {
            return response()->json([
                'success' => false,
                'message' => 'الفصل غير موجود',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id|unique:classrooms,teacher_id,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $classroom->update(['teacher_id' => $request->teacher_id]);
        $classroom->load('teacher');

        return response()->json([
            'success' => true,
            'message' => 'تم تعيين المعلم للفصل بنجاح',
            'data' => $classroom,
        ]);
    }
}
