<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Interfaces\StudentControllerInterface;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller implements StudentControllerInterface
{
    /**
     * عرض قائمة جميع الطلاب
     * GET /api/students
     */
    public function index(): JsonResponse
    {
        $students = Student::with(['classroom', 'grades.subject'])
            ->get()
            ->map(function ($student) {
                $student->gpa = $student->calculateGPA();
                return $student;
            });

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    /**
     * إنشاء طالب جديد
     * POST /api/students
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'nullable|string',
            'guardian_name' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:20',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,transferred',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $student = Student::create($request->all());
        $student->load('classroom');

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الطالب بنجاح',
            'data' => $student,
        ], 201);
    }

    /**
     * عرض طالب محدد
     * GET /api/students/{id}
     */
    public function show(string $id): JsonResponse
    {
        $student = Student::with(['classroom', 'grades.subject'])->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود',
            ], 404);
        }

        $student->gpa = $student->calculateGPA();
        $student->age = $student->age();

        return response()->json([
            'success' => true,
            'data' => $student,
        ]);
    }

    /**
     * تحديث طالب موجود
     * PUT/PATCH /api/students/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:students,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'sometimes|required|date',
            'gender' => 'sometimes|required|in:male,female',
            'address' => 'nullable|string',
            'guardian_name' => 'sometimes|required|string|max:255',
            'guardian_phone' => 'sometimes|required|string|max:20',
            'enrollment_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:active,inactive,graduated,transferred',
            'classroom_id' => 'sometimes|required|exists:classrooms,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $student->update($request->all());
        $student->load('classroom');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الطالب بنجاح',
            'data' => $student,
        ]);
    }

    /**
     * حذف طالب
     * DELETE /api/students/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود',
            ], 404);
        }

        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الطالب بنجاح',
        ]);
    }

    /**
     * الحصول على درجات الطالب
     * GET /api/students/{id}/grades
     */
    public function grades(string $id): JsonResponse
    {
        $student = Student::with(['grades.subject'])->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'student' => $student->only(['id', 'name']),
                'grades' => $student->grades,
                'gpa' => $student->calculateGPA(),
            ],
        ]);
    }

    /**
     * البحث عن الطلاب
     * GET /api/students/search?query=name&classroom_id=1&status=active
     */
    public function search(Request $request): JsonResponse
    {
        $query = Student::query();

        if ($request->has('query')) {
            $searchTerm = $request->query('query');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('guardian_name', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->has('classroom_id')) {
            $query->where('classroom_id', $request->query('classroom_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->has('gender')) {
            $query->where('gender', $request->query('gender'));
        }

        $students = $query->with(['classroom', 'grades.subject'])
            ->get()
            ->map(function ($student) {
                $student->gpa = $student->calculateGPA();
                return $student;
            });

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    /**
     * نقل طالب إلى فصل آخر
     * POST /api/students/{id}/transfer
     */
    public function transfer(Request $request, string $id): JsonResponse
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldClassroom = $student->classroom;
        $student->update(['classroom_id' => $request->classroom_id]);
        $student->load('classroom');

        return response()->json([
            'success' => true,
            'message' => 'تم نقل الطالب بنجاح',
            'data' => [
                'student' => $student,
                'old_classroom' => $oldClassroom,
                'new_classroom' => $student->classroom,
            ],
        ]);
    }
}
