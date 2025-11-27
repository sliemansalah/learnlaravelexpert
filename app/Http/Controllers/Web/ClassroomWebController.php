<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ClassroomWebController extends Controller
{
    /**
     * عرض قائمة الفصول
     */
    public function index(): View
    {
        return view('classrooms.index');
    }

    /**
     * عرض صفحة إضافة فصل جديد
     */
    public function create(): View
    {
        return view('classrooms.create');
    }

    /**
     * عرض تفاصيل فصل محدد
     */
    public function show(string $id): View
    {
        return view('classrooms.show', compact('id'));
    }

    /**
     * عرض صفحة تعديل فصل
     */
    public function edit(string $id): View
    {
        return view('classrooms.edit', compact('id'));
    }
}
