@extends('layouts.app')

@section('title', 'تفاصيل المادة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2">تفاصيل المادة الدراسية</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">المواد الدراسية</a></li>
                    <li class="breadcrumb-item active">تفاصيل المادة</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Subject Details Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-book fa-3x"></i>
                        </div>
                    </div>
                    <h4 class="text-center mb-3" id="subject-name">جاري التحميل...</h4>

                    <div id="subject-details">
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
                        <button onclick="deleteSubject()" class="btn btn-danger">
                            <i class="fas fa-trash"></i> حذف المادة
                        </button>
                        <a href="{{ route('subjects.index') }}" class="btn btn-secondary">
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
                        <div class="col-12 mb-3">
                            <div class="p-3">
                                <h3 class="text-primary mb-2" id="total-students">0</h3>
                                <p class="text-muted mb-0">عدد الطلاب</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3">
                                <h3 class="text-success mb-2" id="average-grade">0</h3>
                                <p class="text-muted mb-0">متوسط الدرجات</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students and Grades -->
        <div class="col-md-8">
            <!-- Students Tab -->
            <div class="card mb-4">
                <div class="card-header bg-transparent">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button">
                                <i class="fas fa-user-graduate"></i> الطلاب
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="grades-tab" data-bs-toggle="tab" data-bs-target="#grades" type="button">
                                <i class="fas fa-chart-line"></i> الدرجات
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Students List -->
                        <div class="tab-pane fade show active" id="students" role="tabpanel">
                            <div id="students-list">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">جاري التحميل...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Grades List -->
                        <div class="tab-pane fade" id="grades" role="tabpanel">
                            <div id="grades-list">
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
                هل أنت متأكد من حذف هذه المادة؟ سيتم حذف جميع البيانات المرتبطة بها.
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
    const subjectId = window.location.pathname.split('/').filter(Boolean).pop();
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let subjectData = null;

    // Load subject details
    async function loadSubject() {
        try {
            const response = await axios.get(`/subjects/${subjectId}`);
            subjectData = response.data.data;
            displaySubject(subjectData);
        } catch (error) {
            console.error('Error loading subject:', error);
            document.getElementById('subject-details').innerHTML = `
                <div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>
            `;
        }
    }

    // Display subject details
    function displaySubject(subject) {
        document.getElementById('subject-name').textContent = subject.name;
        document.getElementById('edit-btn').href = `/subjects/${subject.id}/edit`;

        document.getElementById('subject-details').innerHTML = `
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الكود:</span>
                    <strong><span class="badge bg-secondary">${subject.code}</span></strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الساعات المعتمدة:</span>
                    <strong>${subject.credit_hours} ساعة</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الفصل:</span>
                    <strong>${subject.classroom?.name || '-'}</strong>
                </div>
                ${subject.teacher ? `
                <div class="list-group-item">
                    <span class="text-muted">المعلم:</span><br>
                    <strong>
                        <a href="/teachers/${subject.teacher.id}" class="text-decoration-none">
                            ${subject.teacher.name}
                        </a>
                    </strong>
                </div>
                ` : `
                <div class="list-group-item">
                    <span class="text-muted">المعلم:</span><br>
                    <strong>لا يوجد</strong>
                </div>
                `}
                ${subject.description ? `
                <div class="list-group-item">
                    <span class="text-muted">الوصف:</span><br>
                    <p class="mb-0 mt-2">${subject.description}</p>
                </div>
                ` : ''}
            </div>
        `;
    }

    // Load subject students
    async function loadStudents() {
        try {
            const response = await axios.get(`/subjects/${subjectId}/students`);
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
        document.getElementById('total-students').textContent = students.length;

        if (students.length === 0) {
            document.getElementById('students-list').innerHTML = `
                <div class="alert alert-info">لا يوجد طلاب مسجلين في هذه المادة</div>
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
                            <th>الصف</th>
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
                                <td>${student.grade_level}</td>
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

    // Load subject grades
    async function loadGrades() {
        try {
            const response = await axios.get(`/subjects/${subjectId}/grades`);
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
                <div class="alert alert-info">لا توجد درجات مسجلة لهذه المادة</div>
            `;
            document.getElementById('average-grade').textContent = '0';
            return;
        }

        // Calculate average
        const totalGrade = grades.reduce((sum, grade) => sum + parseFloat(grade.score), 0);
        const average = (totalGrade / grades.length).toFixed(2);
        document.getElementById('average-grade').textContent = average;

        document.getElementById('grades-list').innerHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الطالب</th>
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
                                        <a href="/students/${grade.student_id}" class="text-decoration-none">
                                            ${grade.student?.name || 'N/A'}
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

    // Delete subject
    function deleteSubject() {
        deleteModal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        try {
            await axios.delete(`/subjects/${subjectId}`);
            showAlert('تم حذف المادة بنجاح', 'success');
            setTimeout(() => {
                window.location.href = '{{ route("subjects.index") }}';
            }, 1500);
        } catch (error) {
            console.error('Error deleting subject:', error);
            deleteModal.hide();
        }
    }

    // Load grades tab when clicked
    document.getElementById('grades-tab').addEventListener('click', () => {
        loadGrades();
    });

    // Load data on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadSubject();
        loadStudents();
    });
</script>
@endpush
