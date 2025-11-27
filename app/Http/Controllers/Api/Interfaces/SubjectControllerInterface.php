<?php

namespace App\Http\Controllers\Api\Interfaces;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface SubjectControllerInterface
{
    /**
     * Display a listing of subjects
     */
    public function index(): JsonResponse;

    /**
     * Store a newly created subject
     */
    public function store(Request $request): JsonResponse;

    /**
     * Display the specified subject
     */
    public function show(string $id): JsonResponse;

    /**
     * Update the specified subject
     */
    public function update(Request $request, string $id): JsonResponse;

    /**
     * Remove the specified subject
     */
    public function destroy(string $id): JsonResponse;

    /**
     * Search for subjects
     */
    public function search(Request $request): JsonResponse;

    /**
     * Get students enrolled in a specific subject
     */
    public function students(string $id): JsonResponse;

    /**
     * Get grades for a specific subject
     */
    public function grades(string $id): JsonResponse;

    /**
     * Assign a teacher to a subject
     */
    public function assignTeacher(Request $request, string $id): JsonResponse;
}
