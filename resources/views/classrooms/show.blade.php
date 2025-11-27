@extends('layouts.app')

@section('title', 'تفاصيل الفصل')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2">تفاصيل الفصل</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('classrooms.index') }}">الفصول</a></li>
                    <li class="breadcrumb-item active">تفاصيل الفصل</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Classroom Details Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-door-open fa-3x"></i>
                        </div>
                    </div>
                    <h4 class="text-center mb-3" id="classroom-name">جاري التحميل...</h4>

                    <div id="classroom-details">
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
                        <button onclick="deleteClassroom()" class="btn btn-danger">
                            <i class="fas fa-trash"></i> حذف الفصل
                        </button>
                        <a href="{{ route('classrooms.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
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
                        <div class="col-6 mb-3">
                            <div class="p-3">
                                <h3 class="text-primary mb-2" id="total-students">0</h3>
                                <p class="text-muted mb-0 small">عدد الطلاب</p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3">
                                <h3 class="text-success mb-2" id="capacity">0</h3>
                                <p class="text-muted mb-0 small">السعة</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3">
                                <h3 class="text-info mb-2" id="usage-percentage">0%</h3>
                                <p class="text-muted mb-0 small">نسبة الإشغال</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate"></i> قائمة الطلاب
                    </h5>
                </div>
                <div class="card-body">
                    <div id="students-list">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">جاري التحميل...</span>
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
                هل أنت متأكد من حذف هذا الفصل؟ سيتم حذف جميع البيانات المرتبطة به.
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
    const classroomId = window.location.pathname.split('/').filter(Boolean).pop();
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let classroomData = null;

    // Load classroom details
    async function loadClassroom() {
        try {
            const response = await axios.get(`/classrooms/${classroomId}`);
            classroomData = response.data.data;
            displayClassroom(classroomData);
        } catch (error) {
            console.error('Error loading classroom:', error);
            document.getElementById('classroom-details').innerHTML = `
                <div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>
            `;
        }
    }

    // Display classroom details
    function displayClassroom(classroom) {
        document.getElementById('classroom-name').textContent = classroom.name;
        document.getElementById('edit-btn').href = `/classrooms/${classroom.id}/edit`;

        document.getElementById('classroom-details').innerHTML = `
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الصف:</span>
                    <strong>${classroom.grade_level}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الموقع:</span>
                    <strong>${classroom.location || '-'}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">السعة:</span>
                    <strong>${classroom.capacity}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">عدد الطلاب:</span>
                    <strong>${classroom.students_count || 0}</strong>
                </div>
                ${classroom.teacher ? `
                <div class="list-group-item">
                    <span class="text-muted">المعلم المسؤول:</span><br>
                    <strong>
                        <a href="/teachers/${classroom.teacher.id}" class="text-decoration-none">
                            ${classroom.teacher.name}
                        </a>
                    </strong>
                </div>
                ` : `
                <div class="list-group-item">
                    <span class="text-muted">المعلم المسؤول:</span><br>
                    <strong>لا يوجد</strong>
                </div>
                `}
            </div>
        `;

        // Update statistics
        document.getElementById('total-students').textContent = classroom.students_count || 0;
        document.getElementById('capacity').textContent = classroom.capacity;

        const usagePercentage = classroom.capacity > 0
            ? ((classroom.students_count || 0) / classroom.capacity * 100).toFixed(1)
            : 0;
        document.getElementById('usage-percentage').textContent = usagePercentage + '%';
    }

    // Load classroom students
    async function loadStudents() {
        try {
            const response = await axios.get(`/classrooms/${classroomId}/students`);
            displayStudents(response.data.data);
        } catch (error) {
            console.error('Error loading students:', error);
            document.getElementById('students-list').innerHTML = `
                <div class="alert alert-danger">حدث خطأ في تحميل الطلاب</div>
            `;
        }
    }

    // Display students
    function displayStudents(students) {
        if (students.length === 0) {
            document.getElementById('students-list').innerHTML = `
                <div class="alert alert-info">لا يوجد طلاب في هذا الفصل</div>
            `;
            return;
        }

        document.getElementById('students-list').innerHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الهاتف</th>
                            <th>الجنس</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${students.map(student => `
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
                                <td>
                                    <span class="badge bg-${getStatusColor(student.status)}">
                                        ${getStatusLabel(student.status)}
                                    </span>
                                </td>
                                <td>
                                    <a href="/students/${student.id}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
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

    // Delete classroom
    function deleteClassroom() {
        deleteModal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        try {
            await axios.delete(`/classrooms/${classroomId}`);
            showAlert('تم حذف الفصل بنجاح', 'success');
            setTimeout(() => {
                window.location.href = '{{ route("classrooms.index") }}';
            }, 1500);
        } catch (error) {
            console.error('Error deleting classroom:', error);
            deleteModal.hide();
        }
    }

    // Load data on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadClassroom();
        loadStudents();
    });
</script>
@endpush
