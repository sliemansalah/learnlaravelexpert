<?php

namespace App\Http\Controllers\Api\Interfaces;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface GradeControllerInterface
{
    /**
     * Display a listing of grades
     */
    public function index(): JsonResponse;

    /**
     * Store a newly created grade
     */
    public function store(Request $request): JsonResponse;

    /**
     * Display the specified grade
     */
    public function show(string $id): JsonResponse;

    /**
     * Update the specified grade
     */
    public function update(Request $request, string $id): JsonResponse;

    /**
     * Remove the specified grade
     */
    public function destroy(string $id): JsonResponse;

    /**
     * Search for grades
     */
    public function search(Request $request): JsonResponse;

    /**
     * Get all grades for a specific student
     */
    public function studentGrades(string $student_id): JsonResponse;

    /**
     * Get all grades for a specific subject
     */
    public function subjectGrades(string $subject_id): JsonResponse;

    /**
     * Generate semester report
     */
    public function semesterReport(string $semester): JsonResponse;
}
