@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2">لوحة التحكم</h1>
            <p class="text-muted">مرحباً بك في نظام إدارة المدرسة</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">المعلمين</h6>
                            <h3 class="mb-0" id="teachers-count">0</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-chalkboard-teacher fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">الطلاب</h6>
                            <h3 class="mb-0" id="students-count">0</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-user-graduate fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">الفصول</h6>
                            <h3 class="mb-0" id="classrooms-count">0</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-door-open fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">المواد</h6>
                            <h3 class="mb-0" id="subjects-count">0</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-book fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">الإجراءات السريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('teachers.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-2"></i>
                                إضافة معلم
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('students.create') }}" class="btn btn-success w-100">
                                <i class="fas fa-plus me-2"></i>
                                إضافة طالب
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('classrooms.create') }}" class="btn btn-warning w-100">
                                <i class="fas fa-plus me-2"></i>
                                إضافة فصل
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('subjects.create') }}" class="btn btn-info w-100">
                                <i class="fas fa-plus me-2"></i>
                                إضافة مادة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">أحدث المعلمين</h5>
                </div>
                <div class="card-body">
                    <div id="recent-teachers">
                        <p class="text-center text-muted">جاري التحميل...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">أحدث الطلاب</h5>
                </div>
                <div class="card-body">
                    <div id="recent-students">
                        <p class="text-center text-muted">جاري التحميل...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Load dashboard statistics
    async function loadDashboardStats() {
        try {
            // Load teachers count
            const teachersResponse = await axios.get('/teachers');
            document.getElementById('teachers-count').textContent = teachersResponse.data.data.length;

            // Load students count
            const studentsResponse = await axios.get('/students');
            document.getElementById('students-count').textContent = studentsResponse.data.data.length;

            // Load classrooms count
            const classroomsResponse = await axios.get('/classrooms');
            document.getElementById('classrooms-count').textContent = classroomsResponse.data.data.length;

            // Load subjects count
            const subjectsResponse = await axios.get('/subjects');
            document.getElementById('subjects-count').textContent = subjectsResponse.data.data.length;

            // Load recent teachers
            const recentTeachers = teachersResponse.data.data.slice(0, 5);
            const teachersHtml = recentTeachers.length > 0
                ? recentTeachers.map(teacher => `
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div>
                            <strong>${teacher.name}</strong>
                            <br>
                            <small class="text-muted">${teacher.specialization}</small>
                        </div>
                        <a href="/teachers/${teacher.id}" class="btn btn-sm btn-outline-primary">عرض</a>
                    </div>
                `).join('')
                : '<p class="text-center text-muted">لا يوجد معلمين</p>';
            document.getElementById('recent-teachers').innerHTML = teachersHtml;

            // Load recent students
            const recentStudents = studentsResponse.data.data.slice(0, 5);
            const studentsHtml = recentStudents.length > 0
                ? recentStudents.map(student => `
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div>
                            <strong>${student.name}</strong>
                            <br>
                            <small class="text-muted">${student.email}</small>
                        </div>
                        <a href="/students/${student.id}" class="btn btn-sm btn-outline-success">عرض</a>
                    </div>
                `).join('')
                : '<p class="text-center text-muted">لا يوجد طلاب</p>';
            document.getElementById('recent-students').innerHTML = studentsHtml;

        } catch (error) {
            console.error('Error loading dashboard stats:', error);
        }
    }

    // Load on page ready
    document.addEventListener('DOMContentLoaded', loadDashboardStats);
</script>
@endpush
