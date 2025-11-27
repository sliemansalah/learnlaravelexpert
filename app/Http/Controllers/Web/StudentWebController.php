<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class StudentWebController extends Controller
{
    /**
     * عرض قائمة الطلاب
     */
    public function index(): View
    {
        return view('students.index');
    }

    /**
     * عرض صفحة إضافة طالب جديد
     */
    public function create(): View
    {
        return view('students.create');
    }

    /**
     * عرض تفاصيل طالب محدد
     */
    public function show(string $id): View
    {
        return view('students.show', compact('id'));
    }

    /**
     * عرض صفحة تعديل طالب
     */
    public function edit(string $id): View
    {
        return view('students.edit', compact('id'));
    }
}
