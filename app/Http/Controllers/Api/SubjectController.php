<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Interfaces\SubjectControllerInterface;
use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller implements SubjectControllerInterface
{
    /**
     * عرض قائمة جميع المواد
     * GET /api/subjects
     */
    public function index(): JsonResponse
    {
        $subjects = Subject::with(['teacher'])
            ->withCount('students')
            ->get()
            ->map(function ($subject) {
                $subject->average_score = $subject->averageScore();
                $subject->enrolled_count = $subject->enrolledStudentsCount();
                return $subject;
            });

        return response()->json([
            'success' => true,
            'data' => $subjects,
        ]);
    }

    /**
     * إنشاء مادة جديدة
     * POST /api/subjects
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:subjects,code',
            'description' => 'nullable|string',
            'credit_hours' => 'required|integer|min:1',
            'type' => 'required|in:theoretical,practical,combined',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $subject = Subject::create($request->all());
        $subject->load('teacher');

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المادة بنجاح',
            'data' => $subject,
        ], 201);
    }

    /**
     * عرض مادة محددة
     * GET /api/subjects/{id}
     */
    public function show(string $id): JsonResponse
    {
        $subject = Subject::with(['teacher', 'classroom', 'grades.student'])->find($id);

        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'المادة غير موجودة',
            ], 404);
        }

        $subject->average_score = $subject->averageScore();
        $subject->enrolled_count = $subject->enrolledStudentsCount();
        $subject->highest_score = $subject->highestScore();
        $subject->lowest_score = $subject->lowestScore();

        return response()->json([
            'success' => true,
            'data' => $subject,
        ]);
    }

    /**
     * تحديث مادة موجودة
     * PUT/PATCH /api/subjects/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'المادة غير موجودة',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            'code' => 'sometimes|required|string|max:20|unique:subjects,code,' . $id,
            'description' => 'nullable|string',
            'credit_hours' => 'sometimes|required|integer|min:1',
            'type' => 'sometimes|required|in:theoretical,practical,combined',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $subject->update($request->all());
        $subject->load('teacher');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات المادة بنجاح',
            'data' => $subject,
        ]);
    }

    /**
     * حذف مادة
     * DELETE /api/subjects/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'المادة غير موجودة',
            ], 404);
        }

        $subject->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المادة بنجاح',
        ]);
    }

    /**
     * الحصول على الطلاب المسجلين في المادة
     * GET /api/subjects/{id}/students
     */
    public function students(string $id): JsonResponse
    {
        $subject = Subject::with(['students.classroom'])->find($id);

        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'المادة غير موجودة',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $subject->students,
        ]);
    }

    /**
     * الحصول على درجات المادة
     * GET /api/subjects/{id}/grades
     */
    public function grades(string $id): JsonResponse
    {
        $subject = Subject::with(['grades.student'])->find($id);

        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'المادة غير موجودة',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $subject->grades,
        ]);
    }

    /**
     * البحث عن المواد
     * GET /api/subjects/search?query=name&type=theoretical
     */
    public function search(Request $request): JsonResponse
    {
        $query = Subject::query();

        if ($request->has('query')) {
            $searchTerm = $request->query('query');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('code', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->has('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->query('teacher_id'));
        }

        if ($request->has('credit_hours')) {
            $query->where('credit_hours', $request->query('credit_hours'));
        }

        $subjects = $query->with(['teacher'])
            ->withCount('students')
            ->get()
            ->map(function ($subject) {
                $subject->average_score = $subject->averageScore();
                $subject->enrolled_count = $subject->enrolledStudentsCount();
                return $subject;
            });

        return response()->json([
            'success' => true,
            'data' => $subjects,
        ]);
    }

    /**
     * تعيين معلم للمادة
     * POST /api/subjects/{id}/assign-teacher
     */
    public function assignTeacher(Request $request, string $id): JsonResponse
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'المادة غير موجودة',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $subject->update(['teacher_id' => $request->teacher_id]);
        $subject->load('teacher');

        return response()->json([
            'success' => true,
            'message' => 'تم تعيين المعلم للمادة بنجاح',
            'data' => $subject,
        ]);
    }
}
