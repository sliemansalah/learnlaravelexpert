<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Interfaces\GradeControllerInterface;
use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller implements GradeControllerInterface
{
    /**
     * عرض قائمة جميع الدرجات
     * GET /api/grades
     */
    public function index(): JsonResponse
    {
        $grades = Grade::with(['student', 'subject'])
            ->get()
            ->map(function ($grade) {
                $grade->letter_grade = $grade->getLetterGrade();
                $grade->is_passing = $grade->isPassing();
                return $grade;
            });

        return response()->json([
            'success' => true,
            'data' => $grades,
        ]);
    }

    /**
     * إنشاء درجة جديدة
     * POST /api/grades
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'score' => 'required|numeric|min:0|max:100',
            'semester' => 'required|in:first,second',
            'exam_type' => 'required|in:midterm,final,quiz,assignment',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // التحقق من عدم وجود درجة مكررة لنفس الطالب والمادة والفصل ونوع الامتحان
        $existingGrade = Grade::where('student_id', $request->student_id)
            ->where('subject_id', $request->subject_id)
            ->where('semester', $request->semester)
            ->where('exam_type', $request->exam_type)
            ->first();

        if ($existingGrade) {
            return response()->json([
                'success' => false,
                'message' => 'الدرجة موجودة مسبقاً لهذا الطالب في هذه المادة',
            ], 400);
        }

        $grade = Grade::create($request->all());
        $grade->load(['student', 'subject']);
        $grade->letter_grade = $grade->getLetterGrade();
        $grade->is_passing = $grade->isPassing();

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الدرجة بنجاح',
            'data' => $grade,
        ], 201);
    }

    /**
     * عرض درجة محددة
     * GET /api/grades/{id}
     */
    public function show(string $id): JsonResponse
    {
        $grade = Grade::with(['student', 'subject'])->find($id);

        if (!$grade) {
            return response()->json([
                'success' => false,
                'message' => 'الدرجة غير موجودة',
            ], 404);
        }

        $grade->letter_grade = $grade->getLetterGrade();
        $grade->is_passing = $grade->isPassing();

        return response()->json([
            'success' => true,
            'data' => $grade,
        ]);
    }

    /**
     * تحديث درجة موجودة
     * PUT/PATCH /api/grades/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $grade = Grade::find($id);

        if (!$grade) {
            return response()->json([
                'success' => false,
                'message' => 'الدرجة غير موجودة',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'sometimes|required|exists:students,id',
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'score' => 'sometimes|required|numeric|min:0|max:100',
            'semester' => 'sometimes|required|in:first,second',
            'exam_type' => 'sometimes|required|in:midterm,final,quiz,assignment',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $grade->update($request->all());
        $grade->load(['student', 'subject']);
        $grade->letter_grade = $grade->getLetterGrade();
        $grade->is_passing = $grade->isPassing();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الدرجة بنجاح',
            'data' => $grade,
        ]);
    }

    /**
     * حذف درجة
     * DELETE /api/grades/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $grade = Grade::find($id);

        if (!$grade) {
            return response()->json([
                'success' => false,
                'message' => 'الدرجة غير موجودة',
            ], 404);
        }

        $grade->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الدرجة بنجاح',
        ]);
    }

    /**
     * الحصول على درجات طالب معين
     * GET /api/grades/student/{student_id}
     */
    public function studentGrades(string $studentId): JsonResponse
    {
        $grades = Grade::with(['subject'])
            ->where('student_id', $studentId)
            ->get()
            ->map(function ($grade) {
                $grade->letter_grade = $grade->getLetterGrade();
                $grade->is_passing = $grade->isPassing();
                return $grade;
            });

        if ($grades->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد درجات لهذا الطالب',
            ], 404);
        }

        $average = $grades->avg('score');

        return response()->json([
            'success' => true,
            'data' => [
                'grades' => $grades,
                'statistics' => [
                    'average' => round($average, 2),
                    'count' => $grades->count(),
                    'passing_count' => $grades->where('is_passing', true)->count(),
                    'failing_count' => $grades->where('is_passing', false)->count(),
                ],
            ],
        ]);
    }

    /**
     * الحصول على درجات مادة معينة
     * GET /api/grades/subject/{subject_id}
     */
    public function subjectGrades(string $subjectId): JsonResponse
    {
        $grades = Grade::with(['student'])
            ->where('subject_id', $subjectId)
            ->get()
            ->map(function ($grade) {
                $grade->letter_grade = $grade->getLetterGrade();
                $grade->is_passing = $grade->isPassing();
                return $grade;
            });

        if ($grades->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد درجات لهذه المادة',
            ], 404);
        }

        $average = $grades->avg('score');
        $highest = $grades->max('score');
        $lowest = $grades->min('score');

        return response()->json([
            'success' => true,
            'data' => [
                'grades' => $grades,
                'statistics' => [
                    'average' => round($average, 2),
                    'highest' => $highest,
                    'lowest' => $lowest,
                    'count' => $grades->count(),
                    'passing_count' => $grades->where('is_passing', true)->count(),
                    'failing_count' => $grades->where('is_passing', false)->count(),
                ],
            ],
        ]);
    }

    /**
     * البحث عن الدرجات
     * GET /api/grades/search?semester=first&exam_type=final
     */
    public function search(Request $request): JsonResponse
    {
        $query = Grade::query();

        if ($request->has('student_id')) {
            $query->where('student_id', $request->query('student_id'));
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->query('subject_id'));
        }

        if ($request->has('semester')) {
            $query->where('semester', $request->query('semester'));
        }

        if ($request->has('exam_type')) {
            $query->where('exam_type', $request->query('exam_type'));
        }

        if ($request->has('min_score')) {
            $query->where('score', '>=', $request->query('min_score'));
        }

        if ($request->has('max_score')) {
            $query->where('score', '<=', $request->query('max_score'));
        }

        $grades = $query->with(['student', 'subject'])
            ->get()
            ->map(function ($grade) {
                $grade->letter_grade = $grade->getLetterGrade();
                $grade->is_passing = $grade->isPassing();
                return $grade;
            });

        return response()->json([
            'success' => true,
            'data' => $grades,
        ]);
    }

    /**
     * تقرير الدرجات حسب الفصل الدراسي
     * GET /api/grades/report/semester/{semester}
     */
    public function semesterReport(string $semester): JsonResponse
    {
        if (!in_array($semester, ['first', 'second'])) {
            return response()->json([
                'success' => false,
                'message' => 'الفصل الدراسي غير صحيح',
            ], 400);
        }

        $grades = Grade::with(['student', 'subject'])
            ->where('semester', $semester)
            ->get();

        $statistics = [
            'total_grades' => $grades->count(),
            'average_score' => round($grades->avg('score'), 2),
            'highest_score' => $grades->max('score'),
            'lowest_score' => $grades->min('score'),
            'passing_count' => $grades->filter(fn($g) => $g->isPassing())->count(),
            'failing_count' => $grades->filter(fn($g) => !$g->isPassing())->count(),
            'grade_distribution' => [
                'A' => $grades->filter(fn($g) => $g->score >= 90)->count(),
                'B' => $grades->filter(fn($g) => $g->score >= 80 && $g->score < 90)->count(),
                'C' => $grades->filter(fn($g) => $g->score >= 70 && $g->score < 80)->count(),
                'D' => $grades->filter(fn($g) => $g->score >= 60 && $g->score < 70)->count(),
                'F' => $grades->filter(fn($g) => $g->score < 60)->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'semester' => $semester,
                'statistics' => $statistics,
            ],
        ]);
    }
}
