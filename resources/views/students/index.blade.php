@extends('layouts.app')

@section('title', 'الطلاب')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h2">الطلاب</h1>
            <p class="text-muted">إدارة الطلاب في النظام</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة طالب جديد
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" id="search-query" class="form-control" placeholder="بحث بالاسم أو البريد">
                </div>
                <div class="col-md-2">
                    <select id="status-filter" class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                        <option value="graduated">متخرج</option>
                        <option value="suspended">موقوف</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="classroom-filter" class="form-select">
                        <option value="">كل الفصول</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" id="grade-filter" class="form-control" placeholder="الصف" min="1" max="12">
                </div>
                <div class="col-md-2">
                    <select id="gender-filter" class="form-select">
                        <option value="">كل الأجناس</option>
                        <option value="male">ذكر</option>
                        <option value="female">أنثى</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button onclick="searchStudents()" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الهاتف</th>
                            <th>الجنس</th>
                            <th>تاريخ الميلاد</th>
                            <th>الصف</th>
                            <th>الفصل</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="students-table">
                        <tr>
                            <td colspan="10" class="text-center">جاري التحميل...</td>
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
                هل أنت متأكد من حذف هذا الطالب؟
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

    // Load all students
    async function loadStudents() {
        try {
            const response = await axios.get('/students');
            displayStudents(response.data.data);
        } catch (error) {
            console.error('Error loading students:', error);
            document.getElementById('students-table').innerHTML = `
                <tr><td colspan="10" class="text-center text-danger">حدث خطأ في تحميل البيانات</td></tr>
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

    // Search students
    async function searchStudents() {
        const query = document.getElementById('search-query').value;
        const status = document.getElementById('status-filter').value;
        const classroomId = document.getElementById('classroom-filter').value;
        const gradeLevel = document.getElementById('grade-filter').value;
        const gender = document.getElementById('gender-filter').value;

        try {
            const params = new URLSearchParams();
            if (query) params.append('query', query);
            if (status) params.append('status', status);
            if (classroomId) params.append('classroom_id', classroomId);
            if (gradeLevel) params.append('grade_level', gradeLevel);
            if (gender) params.append('gender', gender);

            const response = await axios.get(`/students/search?${params.toString()}`);
            displayStudents(response.data.data);
        } catch (error) {
            console.error('Error searching students:', error);
        }
    }

    // Display students in table
    function displayStudents(students) {
        const tbody = document.getElementById('students-table');

        if (students.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center">لا توجد بيانات</td></tr>';
            return;
        }

        tbody.innerHTML = students.map(student => `
            <tr>
                <td>${student.id}</td>
                <td>${student.name}</td>
                <td>${student.email}</td>
                <td>${student.phone || '-'}</td>
                <td>
                    <span class="badge bg-${student.gender === 'male' ? 'primary' : 'danger'}">
                        ${student.gender === 'male' ? 'ذكر' : 'أنثى'}
                    </span>
                </td>
                <td>${new Date(student.birth_date).toLocaleDateString('ar-EG')}</td>
                <td>${student.classroom?.grade_level || '-'}</td>
                <td>${student.classroom?.name || '-'}</td>
                <td>
                    <span class="badge bg-${getStatusColor(student.status)}">
                        ${getStatusLabel(student.status)}
                    </span>
                </td>
                <td>
                    <a href="/students/${student.id}" class="btn btn-sm btn-info" title="عرض">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="/students/${student.id}/edit" class="btn btn-sm btn-warning" title="تعديل">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="deleteStudent(${student.id})" class="btn btn-sm btn-danger" title="حذف">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    // Get status color
    function getStatusColor(status) {
        const colors = {
            'active': 'success',
            'inactive': 'secondary',
            'graduated': 'info',
            'suspended': 'danger'
        };
        return colors[status] || 'secondary';
    }

    // Get status label
    function getStatusLabel(status) {
        const labels = {
            'active': 'نشط',
            'inactive': 'غير نشط',
            'graduated': 'متخرج',
            'suspended': 'موقوف'
        };
        return labels[status] || status;
    }

    // Delete student
    function deleteStudent(id) {
        deleteId = id;
        deleteModal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        if (!deleteId) return;

        try {
            await axios.delete(`/students/${deleteId}`);
            showAlert('تم حذف الطالب بنجاح', 'success');
            deleteModal.hide();
            loadStudents();
        } catch (error) {
            console.error('Error deleting student:', error);
        }
    }

    // Load data on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadStudents();
        loadClassrooms();
    });

    // Search on Enter key
    document.getElementById('search-query').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') searchStudents();
    });
</script>
@endpush
