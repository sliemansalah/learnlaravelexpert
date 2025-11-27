<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class GradeWebController extends Controller
{
    /**
     * عرض قائمة الدرجات
     */
    public function index(): View
    {
        return view('grades.index');
    }

    /**
     * عرض صفحة إضافة درجة جديدة
     */
    public function create(): View
    {
        return view('grades.create');
    }

    /**
     * عرض تفاصيل درجة محددة
     */
    public function show(string $id): View
    {
        return view('grades.show', compact('id'));
    }

    /**
     * عرض صفحة تعديل درجة
     */
    public function edit(string $id): View
    {
        return view('grades.edit', compact('id'));
    }
}
