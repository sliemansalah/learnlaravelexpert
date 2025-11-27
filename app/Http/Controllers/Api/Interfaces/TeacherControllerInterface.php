<?php

namespace App\Http\Controllers\Api\Interfaces;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface TeacherControllerInterface
{
    /**
     * Display a listing of teachers
     */
    public function index(): JsonResponse;

    /**
     * Store a newly created teacher
     */
    public function store(Request $request): JsonResponse;

    /**
     * Display the specified teacher
     */
    public function show(string $id): JsonResponse;

    /**
     * Update the specified teacher
     */
    public function update(Request $request, string $id): JsonResponse;

    /**
     * Remove the specified teacher
     */
    public function destroy(string $id): JsonResponse;

    /**
     * Search for teachers
     */
    public function search(Request $request): JsonResponse;

    /**
     * Get subjects for a specific teacher
     */
    public function subjects(string $id): JsonResponse;
}
