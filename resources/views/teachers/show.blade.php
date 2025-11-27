@extends('layouts.app')

@section('title', 'تفاصيل المعلم')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2">تفاصيل المعلم</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">المعلمين</a></li>
                    <li class="breadcrumb-item active">تفاصيل المعلم</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Teacher Details Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-user fa-3x"></i>
                        </div>
                    </div>
                    <h4 class="text-center mb-3" id="teacher-name">جاري التحميل...</h4>

                    <div id="teacher-details">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">جاري التحميل...</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="#" id="edit-btn" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل البيانات
                        </a>
                        <button onclick="deleteTeacher()" class="btn btn-danger">
                            <i class="fas fa-trash"></i> حذف المعلم
                        </button>
                        <a href="{{ route('teachers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Subjects -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-book"></i> المواد الدراسية
                    </h5>
                </div>
                <div class="card-body">
                    <div id="subjects-list">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">جاري التحميل...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card mt-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i> الإحصائيات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3">
                                <h3 class="text-primary mb-2" id="total-subjects">0</h3>
                                <p class="text-muted mb-0">إجمالي المواد</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3">
                                <h3 class="text-success mb-2" id="total-students">0</h3>
                                <p class="text-muted mb-0">إجمالي الطلاب</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3">
                                <h3 class="text-info mb-2" id="years-experience">0</h3>
                                <p class="text-muted mb-0">سنوات الخبرة</p>
                            </div>
                        </div>
                    </div>
                </div>
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
                هل أنت متأكد من حذف هذا المعلم؟ سيتم حذف جميع البيانات المرتبطة به.
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
    const teacherId = window.location.pathname.split('/').filter(Boolean).pop();
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let teacherData = null;

    // Load teacher details
    async function loadTeacher() {
        try {
            const response = await axios.get(`/teachers/${teacherId}`);
            teacherData = response.data.data;
            displayTeacher(teacherData);
        } catch (error) {
            console.error('Error loading teacher:', error);
            document.getElementById('teacher-details').innerHTML = `
                <div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>
            `;
        }
    }

    // Display teacher details
    function displayTeacher(teacher) {
        document.getElementById('teacher-name').textContent = teacher.name;
        document.getElementById('edit-btn').href = `/teachers/${teacher.id}/edit`;

        const statusBadge = `
            <span class="badge bg-${getStatusColor(teacher.status)}">
                ${getStatusLabel(teacher.status)}
            </span>
        `;

        document.getElementById('teacher-details').innerHTML = `
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">البريد الإلكتروني:</span>
                    <strong>${teacher.email}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الهاتف:</span>
                    <strong>${teacher.phone || '-'}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">التخصص:</span>
                    <strong>${teacher.specialization}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">تاريخ التوظيف:</span>
                    <strong>${new Date(teacher.hire_date).toLocaleDateString('ar-EG')}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الراتب:</span>
                    <strong>${parseFloat(teacher.salary).toLocaleString('ar-EG')} جنيه</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الحالة:</span>
                    ${statusBadge}
                </div>
            </div>
        `;

        // Calculate years of experience
        const hireDate = new Date(teacher.hire_date);
        const years = Math.floor((new Date() - hireDate) / (365.25 * 24 * 60 * 60 * 1000));
        document.getElementById('years-experience').textContent = years;
    }

    // Load teacher subjects
    async function loadSubjects() {
        try {
            const response = await axios.get(`/teachers/${teacherId}/subjects`);
            displaySubjects(response.data.data);
        } catch (error) {
            console.error('Error loading subjects:', error);
            document.getElementById('subjects-list').innerHTML = `
                <div class="alert alert-danger">حدث خطأ في تحميل المواد</div>
            `;
        }
    }

    // Display subjects
    function displaySubjects(subjects) {
        document.getElementById('total-subjects').textContent = subjects.length;

        // Calculate total students
        const totalStudents = subjects.reduce((sum, subject) => sum + (subject.students_count || 0), 0);
        document.getElementById('total-students').textContent = totalStudents;

        if (subjects.length === 0) {
            document.getElementById('subjects-list').innerHTML = `
                <div class="alert alert-info">لا يوجد مواد دراسية مسندة لهذا المعلم</div>
            `;
            return;
        }

        document.getElementById('subjects-list').innerHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>اسم المادة</th>
                            <th>الكود</th>
                            <th>الساعات الأسبوعية</th>
                            <th>عدد الطلاب</th>
                            <th>الفصل</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${subjects.map(subject => `
                            <tr>
                                <td>
                                    <a href="/subjects/${subject.id}" class="text-decoration-none">
                                        ${subject.name}
                                    </a>
                                </td>
                                <td>${subject.code}</td>
                                <td>${subject.weekly_hours}</td>
                                <td>
                                    <span class="badge bg-info">${subject.students_count || 0}</span>
                                </td>
                                <td>${subject.classroom?.name || '-'}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
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
    function deleteTeacher() {
        deleteModal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        try {
            await axios.delete(`/teachers/${teacherId}`);
            showAlert('تم حذف المعلم بنجاح', 'success');
            setTimeout(() => {
                window.location.href = '{{ route("teachers.index") }}';
            }, 1500);
        } catch (error) {
            console.error('Error deleting teacher:', error);
            deleteModal.hide();
        }
    }

    // Load data on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadTeacher();
        loadSubjects();
    });
</script>
@endpush
