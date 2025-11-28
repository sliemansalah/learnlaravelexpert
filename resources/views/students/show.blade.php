@extends('layouts.app')

@section('title', 'تفاصيل الطالب')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2">تفاصيل الطالب</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">الطلاب</a></li>
                    <li class="breadcrumb-item active">تفاصيل الطالب</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Student Details Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-user-graduate fa-3x"></i>
                        </div>
                    </div>
                    <h4 class="text-center mb-3" id="student-name">جاري التحميل...</h4>

                    <div id="student-details">
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
                        <button onclick="deleteStudent()" class="btn btn-danger">
                            <i class="fas fa-trash"></i> حذف الطالب
                        </button>
                        <a href="{{ route('students.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Grades -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line"></i> الدرجات
                    </h5>
                </div>
                <div class="card-body">
                    <div id="grades-list">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">جاري التحميل...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card">
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
                                <h3 class="text-success mb-2" id="average-grade">0</h3>
                                <p class="text-muted mb-0">المعدل العام</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3">
                                <h3 class="text-info mb-2" id="age">0</h3>
                                <p class="text-muted mb-0">العمر</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parent Information Card -->
            <div class="card mt-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> معلومات ولي الأمر
                    </h5>
                </div>
                <div class="card-body" id="parent-info">
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

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من حذف هذا الطالب؟ سيتم حذف جميع البيانات المرتبطة به.
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
    const studentId = window.location.pathname.split('/').filter(Boolean).pop();
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let studentData = null;

    // Load student details
    async function loadStudent() {
        try {
            const response = await axios.get(`/students/${studentId}`);
            studentData = response.data.data;
            displayStudent(studentData);
        } catch (error) {
            console.error('Error loading student:', error);
            document.getElementById('student-details').innerHTML = `
                <div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>
            `;
        }
    }

    // Display student details
    function displayStudent(student) {
        document.getElementById('student-name').textContent = student.name;
        document.getElementById('edit-btn').href = `/students/${student.id}/edit`;

        const statusBadge = `
            <span class="badge bg-${getStatusColor(student.status)}">
                ${getStatusLabel(student.status)}
            </span>
        `;

        const genderBadge = `
            <span class="badge bg-${student.gender === 'male' ? 'primary' : 'danger'}">
                ${student.gender === 'male' ? 'ذكر' : 'أنثى'}
            </span>
        `;

        document.getElementById('student-details').innerHTML = `
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">البريد الإلكتروني:</span>
                    <strong>${student.email}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الهاتف:</span>
                    <strong>${student.phone || '-'}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الجنس:</span>
                    ${genderBadge}
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">تاريخ الميلاد:</span>
                    <strong>${new Date(student.birth_date).toLocaleDateString('ar-EG')}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الصف:</span>
                    <strong>${student.classroom?.grade_level || '-'}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الفصل:</span>
                    <strong>${student.classroom?.name || '-'}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الحالة:</span>
                    ${statusBadge}
                </div>
                ${student.address ? `
                <div class="list-group-item">
                    <span class="text-muted">العنوان:</span><br>
                    <strong>${student.address}</strong>
                </div>
                ` : ''}
            </div>
        `;

        // Display parent information
        document.getElementById('parent-info').innerHTML = `
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">اسم ولي الأمر:</span>
                    <strong>${student.guardian_name || '-'}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">هاتف ولي الأمر:</span>
                    <strong>${student.guardian_phone || '-'}</strong>
                </div>
            </div>
        `;

        // Calculate age
        const birthDate = new Date(student.birth_date);
        const age = Math.floor((new Date() - birthDate) / (365.25 * 24 * 60 * 60 * 1000));
        document.getElementById('age').textContent = age;
    }

    // Load student grades
    async function loadGrades() {
        try {
            const response = await axios.get(`/students/${studentId}/grades`);
            displayGrades(response.data.data);
        } catch (error) {
            console.error('Error loading grades:', error);
            document.getElementById('grades-list').innerHTML = `
                <div class="alert alert-danger">حدث خطأ في تحميل الدرجات</div>
            `;
        }
    }

    // Display grades
    function displayGrades(grades) {
        if (grades.length === 0) {
            document.getElementById('grades-list').innerHTML = `
                <div class="alert alert-info">لا توجد درجات مسجلة لهذا الطالب</div>
            `;
            document.getElementById('average-grade').textContent = '0';
            document.getElementById('total-subjects').textContent = '0';
            return;
        }

        // Calculate unique subjects count
        const uniqueSubjects = new Set(grades.map(grade => grade.subject_id));
        document.getElementById('total-subjects').textContent = uniqueSubjects.size;

        // Calculate average
        const totalGrade = grades.reduce((sum, grade) => sum + parseFloat(grade.score), 0);
        const average = (totalGrade / grades.length).toFixed(2);
        document.getElementById('average-grade').textContent = average;

        document.getElementById('grades-list').innerHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>المادة</th>
                            <th>الفصل الدراسي</th>
                            <th>نوع الامتحان</th>
                            <th>الدرجة</th>
                            <th>التقدير</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${grades.map(grade => {
                            const score = parseFloat(grade.score);
                            return `
                                <tr>
                                    <td>
                                        <a href="/subjects/${grade.subject_id}" class="text-decoration-none">
                                            ${grade.subject?.name || 'N/A'}
                                        </a>
                                    </td>
                                    <td>${getSemesterLabel(grade.semester)}</td>
                                    <td>${getExamTypeLabel(grade.exam_type)}</td>
                                    <td><strong>${score}</strong></td>
                                    <td>
                                        <span class="badge bg-${getGradeColor(score)}">
                                            ${getGradeLabel(score)}
                                        </span>
                                    </td>
                                </tr>
                            `;
                        }).join('')}
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

    // Get semester label
    function getSemesterLabel(semester) {
        const labels = {
            'first': 'الأول',
            'second': 'الثاني'
        };
        return labels[semester] || semester;
    }

    // Get exam type label
    function getExamTypeLabel(examType) {
        const labels = {
            'midterm': 'نصفي',
            'final': 'نهائي',
            'quiz': 'اختبار قصير',
            'assignment': 'واجب'
        };
        return labels[examType] || examType;
    }

    // Get grade color based on score
    function getGradeColor(score) {
        if (score >= 90) return 'success';
        if (score >= 80) return 'primary';
        if (score >= 70) return 'info';
        if (score >= 60) return 'warning';
        return 'danger';
    }

    // Get grade label based on score
    function getGradeLabel(score) {
        if (score >= 90) return 'ممتاز (A)';
        if (score >= 80) return 'جيد جداً (B)';
        if (score >= 70) return 'جيد (C)';
        if (score >= 60) return 'مقبول (D)';
        return 'راسب (F)';
    }

    // Delete student
    function deleteStudent() {
        deleteModal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        try {
            await axios.delete(`/students/${studentId}`);
            showAlert('تم حذف الطالب بنجاح', 'success');
            setTimeout(() => {
                window.location.href = '{{ route("students.index") }}';
            }, 1500);
        } catch (error) {
            console.error('Error deleting student:', error);
            deleteModal.hide();
        }
    }

    // Load data on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadStudent();
        loadGrades();
    });
</script>
@endpush
