<?php

namespace App\Http\Controllers\Api\Interfaces;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ClassroomControllerInterface
{
    /**
     * Display a listing of classrooms
     */
    public function index(): JsonResponse;

    /**
     * Store a newly created classroom
     */
    public function store(Request $request): JsonResponse;

    /**
     * Display the specified classroom
     */
    public function show(string $id): JsonResponse;

    /**
     * Update the specified classroom
     */
    public function update(Request $request, string $id): JsonResponse;

    /**
     * Remove the specified classroom
     */
    public function destroy(string $id): JsonResponse;

    /**
     * Search for classrooms
     */
    public function search(Request $request): JsonResponse;

    /**
     * Get students in a specific classroom
     */
    public function students(string $id): JsonResponse;

    /**
     * Assign a teacher to a classroom
     */
    public function assignTeacher(Request $request, string $id): JsonResponse;
}
