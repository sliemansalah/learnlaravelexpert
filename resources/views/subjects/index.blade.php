@extends('layouts.app')

@section('title', 'المواد الدراسية')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h2">المواد الدراسية</h1>
            <p class="text-muted">إدارة المواد الدراسية في النظام</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة مادة جديدة
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" id="search-query" class="form-control" placeholder="بحث بالاسم أو الكود">
                </div>
                <div class="col-md-3">
                    <select id="classroom-filter" class="form-select">
                        <option value="">كل الفصول</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="teacher-filter" class="form-select">
                        <option value="">كل المعلمين</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button onclick="searchSubjects()" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Subjects Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>اسم المادة</th>
                            <th>الكود</th>
                            <th>الوصف</th>
                            <th>الساعات الأسبوعية</th>
                            <th>الفصل</th>
                            <th>المعلم</th>
                            <th>عدد الطلاب</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="subjects-table">
                        <tr>
                            <td colspan="9" class="text-center">جاري التحميل...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من حذف هذه المادة؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">حذف</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let deleteId = null;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    // Load all subjects
    async function loadSubjects() {
        try {
            const response = await axios.get('/subjects');
            displaySubjects(response.data.data);
        } catch (error) {
            console.error('Error loading subjects:', error);
            document.getElementById('subjects-table').innerHTML = `
                <tr><td colspan="9" class="text-center text-danger">حدث خطأ في تحميل البيانات</td></tr>
            `;
        }
    }

    // Load classrooms for filter
    async function loadClassrooms() {
        try {
            const response = await axios.get('/classrooms');
            const select = document.getElementById('classroom-filter');
            response.data.data.forEach(classroom => {
                const option = document.createElement('option');
                option.value = classroom.id;
                option.textContent = classroom.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading classrooms:', error);
        }
    }

    // Load teachers for filter
    async function loadTeachers() {
        try {
            const response = await axios.get('/teachers');
            const select = document.getElementById('teacher-filter');
            response.data.data.forEach(teacher => {
                const option = document.createElement('option');
                option.value = teacher.id;
                option.textContent = teacher.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading teachers:', error);
        }
    }

    // Search subjects
    async function searchSubjects() {
        const query = document.getElementById('search-query').value;
        const classroomId = document.getElementById('classroom-filter').value;
        const teacherId = document.getElementById('teacher-filter').value;

        try {
            const params = new URLSearchParams();
            if (query) params.append('query', query);
            if (classroomId) params.append('classroom_id', classroomId);
            if (teacherId) params.append('teacher_id', teacherId);

            const response = await axios.get(`/subjects/search?${params.toString()}`);
            displaySubjects(response.data.data);
        } catch (error) {
            console.error('Error searching subjects:', error);
        }
    }

    // Display subjects in table
    function displaySubjects(subjects) {
        const tbody = document.getElementById('subjects-table');

        if (subjects.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center">لا توجد بيانات</td></tr>';
            return;
        }

        tbody.innerHTML = subjects.map(subject => `
            <tr>
                <td>${subject.id}</td>
                <td>
                    <strong>${subject.name}</strong>
                </td>
                <td>
                    <span class="badge bg-secondary">${subject.code}</span>
                </td>
                <td>${subject.description ? (subject.description.substring(0, 50) + '...') : '-'}</td>
                <td>
                    <span class="badge bg-info">${subject.weekly_hours} ساعة</span>
                </td>
                <td>${subject.classroom?.name || '-'}</td>
                <td>
                    ${subject.teacher ? `
                        <a href="/teachers/${subject.teacher.id}" class="text-decoration-none">
                            ${subject.teacher.name}
                        </a>
                    ` : '-'}
                </td>
                <td>
                    <span class="badge bg-primary">${subject.students_count || 0}</span>
                </td>
                <td>
                    <a href="/subjects/${subject.id}" class="btn btn-sm btn-info" title="عرض">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="/subjects/${subject.id}/edit" class="btn btn-sm btn-warning" title="تعديل">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="deleteSubject(${subject.id})" class="btn btn-sm btn-danger" title="حذف">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    // Delete subject
    function deleteSubject(id) {
        deleteId = id;
        deleteModal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        if (!deleteId) return;

        try {
            await axios.delete(`/subjects/${deleteId}`);
            showAlert('تم حذف المادة بنجاح', 'success');
            deleteModal.hide();
            loadSubjects();
        } catch (error) {
            console.error('Error deleting subject:', error);
        }
    }

    // Load data on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadSubjects();
        loadClassrooms();
        loadTeachers();
    });

    // Search on Enter key
    document.getElementById('search-query').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') searchSubjects();
    });
</script>
@endpush
