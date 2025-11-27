<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TeacherWebController extends Controller
{
    /**
     * عرض قائمة المعلمين
     */
    public function index(): View
    {
        return view('teachers.index');
    }

    /**
     * عرض صفحة إضافة معلم جديد
     */
    public function create(): View
    {
        return view('teachers.create');
    }

    /**
     * عرض تفاصيل معلم محدد
     */
    public function show(string $id): View
    {
        return view('teachers.show', compact('id'));
    }

    /**
     * عرض صفحة تعديل معلم
     */
    public function edit(string $id): View
    {
        return view('teachers.edit', compact('id'));
    }
}
