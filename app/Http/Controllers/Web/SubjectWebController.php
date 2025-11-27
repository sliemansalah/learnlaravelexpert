<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SubjectWebController extends Controller
{
    /**
     * عرض قائمة المواد
     */
    public function index(): View
    {
        return view('subjects.index');
    }

    /**
     * عرض صفحة إضافة مادة جديدة
     */
    public function create(): View
    {
        return view('subjects.create');
    }

    /**
     * عرض تفاصيل مادة محددة
     */
    public function show(string $id): View
    {
        return view('subjects.show', compact('id'));
    }

    /**
     * عرض صفحة تعديل مادة
     */
    public function edit(string $id): View
    {
        return view('subjects.edit', compact('id'));
    }
}
