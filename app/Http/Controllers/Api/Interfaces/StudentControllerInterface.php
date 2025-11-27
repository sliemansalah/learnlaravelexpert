<?php

namespace App\Http\Controllers\Api\Interfaces;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface StudentControllerInterface
{
    /**
     * Display a listing of students
     */
    public function index(): JsonResponse;

    /**
     * Store a newly created student
     */
    public function store(Request $request): JsonResponse;

    /**
     * Display the specified student
     */
    public function show(string $id): JsonResponse;

    /**
     * Update the specified student
     */
    public function update(Request $request, string $id): JsonResponse;

    /**
     * Remove the specified student
     */
    public function destroy(string $id): JsonResponse;

    /**
     * Search for students
     */
    public function search(Request $request): JsonResponse;

    /**
     * Get grades for a specific student
     */
    public function grades(string $id): JsonResponse;

    /**
     * Transfer student to another classroom
     */
    public function transfer(Request $request, string $id): JsonResponse;
}
