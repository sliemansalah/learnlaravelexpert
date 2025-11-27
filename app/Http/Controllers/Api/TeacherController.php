<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Interfaces\TeacherControllerInterface;
use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller implements TeacherControllerInterface
{
    /**
     * عرض قائمة جميع المعلمين
     * GET /api/teachers
     */
    public function index(): JsonResponse
    {
        $teachers = Teacher::with(['classroom', 'subjects'])
            ->withCount('subjects')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $teachers,
        ]);
    }

    /**
     * إنشاء معلم جديد
     * POST /api/teachers
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'required|string|max:100',
            'hire_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,on_leave',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $teacher = Teacher::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المعلم بنجاح',
            'data' => $teacher,
        ], 201);
    }

    /**
     * عرض معلم محدد
     * GET /api/teachers/{id}
     */
    public function show(string $id): JsonResponse
    {
        $teacher = Teacher::with(['classroom.students', 'subjects'])
            ->withCount('subjects')
            ->find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'المعلم غير موجود',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $teacher,
        ]);
    }

    /**
     * تحديث معلم موجود
     * PUT/PATCH /api/teachers/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $teacher = Teacher::find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'المعلم غير موجود',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:teachers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'specialization' => 'sometimes|required|string|max:100',
            'hire_date' => 'sometimes|required|date',
            'salary' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:active,inactive,on_leave',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $teacher->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات المعلم بنجاح',
            'data' => $teacher,
        ]);
    }

    /**
     * حذف معلم
     * DELETE /api/teachers/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $teacher = Teacher::find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'المعلم غير موجود',
            ], 404);
        }

        $teacher->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المعلم بنجاح',
        ]);
    }

    /**
     * الحصول على المواد التي يدرسها المعلم
     * GET /api/teachers/{id}/subjects
     */
    public function subjects(string $id): JsonResponse
    {
        $teacher = Teacher::with('subjects')->find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'المعلم غير موجود',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $teacher->subjects,
        ]);
    }

    /**
     * البحث عن المعلمين
     * GET /api/teachers/search?query=name&status=active
     */
    public function search(Request $request): JsonResponse
    {
        $query = Teacher::query();

        if ($request->has('query')) {
            $searchTerm = $request->query('query');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('specialization', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->has('specialization')) {
            $query->where('specialization', $request->query('specialization'));
        }

        $teachers = $query->with(['classroom', 'subjects'])
            ->withCount('subjects')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $teachers,
        ]);
    }
}
