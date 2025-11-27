@extends('layouts.app')

@section('title', 'المعلمين')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h2">المعلمين</h1>
            <p class="text-muted">إدارة المعلمين في النظام</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('teachers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة معلم جديد
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" id="search-query" class="form-control" placeholder="بحث بالاسم، البريد، أو التخصص">
                </div>
                <div class="col-md-3">
                    <select id="status-filter" class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                        <option value="on_leave">في إجازة</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" id="specialization-filter" class="form-control" placeholder="التخصص">
                </div>
                <div class="col-md-2">
                    <button onclick="searchTeachers()" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Teachers Table -->
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
                            <th>التخصص</th>
                            <th>الراتب</th>
                            <th>الحالة</th>
                            <th>عدد المواد</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="teachers-table">
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
                هل أنت متأكد من حذف هذا المعلم؟
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

    // Load all teachers
    async function loadTeachers() {
        try {
            const response = await axios.get('/teachers');
            displayTeachers(response.data.data);
        } catch (error) {
            console.error('Error loading teachers:', error);
            document.getElementById('teachers-table').innerHTML = `
                <tr><td colspan="9" class="text-center text-danger">حدث خطأ في تحميل البيانات</td></tr>
            `;
        }
    }

    // Search teachers
    async function searchTeachers() {
        const query = document.getElementById('search-query').value;
        const status = document.getElementById('status-filter').value;
        const specialization = document.getElementById('specialization-filter').value;

        try {
            const params = new URLSearchParams();
            if (query) params.append('query', query);
            if (status) params.append('status', status);
            if (specialization) params.append('specialization', specialization);

            const response = await axios.get(`/teachers/search?${params.toString()}`);
            displayTeachers(response.data.data);
        } catch (error) {
            console.error('Error searching teachers:', error);
        }
    }

    // Display teachers in table
    function displayTeachers(teachers) {
        const tbody = document.getElementById('teachers-table');

        if (teachers.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center">لا توجد بيانات</td></tr>';
            return;
        }

        tbody.innerHTML = teachers.map(teacher => `
            <tr>
                <td>${teacher.id}</td>
                <td>${teacher.name}</td>
                <td>${teacher.email}</td>
                <td>${teacher.phone || '-'}</td>
                <td>${teacher.specialization}</td>
                <td>${parseFloat(teacher.salary).toLocaleString('ar-EG')} جنيه</td>
                <td>
                    <span class="badge bg-${getStatusColor(teacher.status)}">
                        ${getStatusLabel(teacher.status)}
                    </span>
                </td>
                <td>${teacher.subjects_count || 0}</td>
                <td>
                    <a href="/teachers/${teacher.id}" class="btn btn-sm btn-info" title="عرض">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="/teachers/${teacher.id}/edit" class="btn btn-sm btn-warning" title="تعديل">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="deleteTeacher(${teacher.id})" class="btn btn-sm btn-danger" title="حذف">
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
            'inactive': 'danger',
            'on_leave': 'warning'
        };
        return colors[status] || 'secondary';
    }

    // Get status label
    function getStatusLabel(status) {
        const labels = {
            'active': 'نشط',
            'inactive': 'غير نشط',
            'on_leave': 'في إجازة'
        };
        return labels[status] || status;
    }

    // Delete teacher
    function deleteTeacher(id) {
        deleteId = id;
        deleteModal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        if (!deleteId) return;

        try {
            await axios.delete(`/teachers/${deleteId}`);
            showAlert('تم حذف المعلم بنجاح', 'success');
            deleteModal.hide();
            loadTeachers();
        } catch (error) {
            console.error('Error deleting teacher:', error);
        }
    }

    // Load teachers on page load
    document.addEventListener('DOMContentLoaded', loadTeachers);

    // Search on Enter key
    document.getElementById('search-query').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') searchTeachers();
    });
</script>
@endpush
