@extends('layouts.app')

@section('title', 'تفاصيل الدرجة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2">تفاصيل الدرجة</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('grades.index') }}">الدرجات</a></li>
                    <li class="breadcrumb-item active">تفاصيل الدرجة</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Grade Details Card -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                            <div>
                                <h2 class="mb-0" id="grade-percentage">0%</h2>
                                <small id="grade-label">-</small>
                            </div>
                        </div>
                    </div>

                    <div id="grade-details">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">جاري التحميل...</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="#" id="edit-btn" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل الدرجة
                        </a>
                        <button onclick="deleteGrade()" class="btn btn-danger">
                            <i class="fas fa-trash"></i> حذف الدرجة
                        </button>
                        <a href="{{ route('grades.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="col-md-6">
            <!-- Student Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate"></i> معلومات الطالب
                    </h5>
                </div>
                <div class="card-body" id="student-info">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subject Info Card -->
            <div class="card">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-book"></i> معلومات المادة
                    </h5>
                </div>
                <div class="card-body" id="subject-info">
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
                هل أنت متأكد من حذف هذه الدرجة؟
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
    const gradeId = window.location.pathname.split('/').filter(Boolean).pop();
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let gradeData = null;

    // Load grade details
    async function loadGrade() {
        try {
            const response = await axios.get(`/grades/${gradeId}`);
            gradeData = response.data.data;
            displayGrade(gradeData);
        } catch (error) {
            console.error('Error loading grade:', error);
            document.getElementById('grade-details').innerHTML = `
                <div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>
            `;
        }
    }

    // Display grade details
    function displayGrade(grade) {
        document.getElementById('edit-btn').href = `/grades/${grade.id}/edit`;

        const percentage = (parseFloat(grade.grade) / parseFloat(grade.max_grade)) * 100;

        // Update circle badge
        const gradePercentageEl = document.getElementById('grade-percentage');
        gradePercentageEl.textContent = percentage.toFixed(1) + '%';
        gradePercentageEl.parentElement.parentElement.className =
            `bg-${getGradeColor(percentage)} text-white rounded-circle d-inline-flex align-items-center justify-content-center`;
        document.getElementById('grade-label').textContent = getGradeLabel(percentage);

        // Display grade details
        document.getElementById('grade-details').innerHTML = `
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الدرجة:</span>
                    <strong class="h4 mb-0">${grade.grade} / ${grade.max_grade}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">الفصل الدراسي:</span>
                    <strong>${getSemesterLabel(grade.semester)}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">التقدير:</span>
                    <span class="badge bg-${getGradeColor(percentage)} fs-6">
                        ${getGradeLabel(percentage)}
                    </span>
                </div>
                ${grade.remarks ? `
                <div class="list-group-item">
                    <span class="text-muted">ملاحظات:</span><br>
                    <p class="mb-0 mt-2">${grade.remarks}</p>
                </div>
                ` : ''}
            </div>
        `;

        // Display student info
        if (grade.student) {
            document.getElementById('student-info').innerHTML = `
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">الاسم:</span>
                        <strong>
                            <a href="/students/${grade.student.id}" class="text-decoration-none">
                                ${grade.student.name}
                            </a>
                        </strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">البريد:</span>
                        <strong>${grade.student.email}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">الصف:</span>
                        <strong>${grade.student.grade_level}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">الفصل:</span>
                        <strong>${grade.student.classroom?.name || '-'}</strong>
                    </div>
                </div>
            `;
        }

        // Display subject info
        if (grade.subject) {
            document.getElementById('subject-info').innerHTML = `
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">المادة:</span>
                        <strong>
                            <a href="/subjects/${grade.subject.id}" class="text-decoration-none">
                                ${grade.subject.name}
                            </a>
                        </strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">الكود:</span>
                        <strong><span class="badge bg-secondary">${grade.subject.code}</span></strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">الساعات الأسبوعية:</span>
                        <strong>${grade.subject.weekly_hours} ساعة</strong>
                    </div>
                    ${grade.subject.teacher ? `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">المعلم:</span>
                        <strong>
                            <a href="/teachers/${grade.subject.teacher.id}" class="text-decoration-none">
                                ${grade.subject.teacher.name}
                            </a>
                        </strong>
                    </div>
                    ` : ''}
                </div>
            `;
        }
    }

    // Get semester label
    function getSemesterLabel(semester) {
        const labels = {
            'first': 'الفصل الأول',
            'second': 'الفصل الثاني'
        };
        return labels[semester] || semester;
    }

    // Get grade color
    function getGradeColor(percentage) {
        if (percentage >= 85) return 'success';
        if (percentage >= 70) return 'primary';
        if (percentage >= 60) return 'warning';
        return 'danger';
    }

    // Get grade label
    function getGradeLabel(percentage) {
        if (percentage >= 85) return 'ممتاز';
        if (percentage >= 70) return 'جيد جدا';
        if (percentage >= 60) return 'جيد';
        if (percentage >= 50) return 'مقبول';
        return 'راسب';
    }

    // Delete grade
    function deleteGrade() {
        deleteModal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        try {
            await axios.delete(`/grades/${gradeId}`);
            showAlert('تم حذف الدرجة بنجاح', 'success');
            setTimeout(() => {
                window.location.href = '{{ route("grades.index") }}';
            }, 1500);
        } catch (error) {
            console.error('Error deleting grade:', error);
            deleteModal.hide();
        }
    }

    // Load data on page load
    document.addEventListener('DOMContentLoaded', loadGrade);
</script>
@endpush
