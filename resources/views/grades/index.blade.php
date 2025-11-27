@extends('layouts.app')

@section('title', 'الدرجات')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h2">الدرجات</h1>
            <p class="text-muted">إدارة درجات الطلاب في النظام</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('grades.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة درجة جديدة
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <select id="student-filter" class="form-select">
                        <option value="">كل الطلاب</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="subject-filter" class="form-select">
                        <option value="">كل المواد</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="semester-filter" class="form-select">
                        <option value="">كل الفصول</option>
                        <option value="first">الفصل الأول</option>
                        <option value="second">الفصل الثاني</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" id="min-grade" class="form-control" placeholder="من درجة" min="0">
                </div>
                <div class="col-md-2">
                    <button onclick="searchGrades()" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الطالب</th>
                            <th>المادة</th>
                            <th>الفصل الدراسي</th>
                            <th>الدرجة</th>
                            <th>الحد الأقصى</th>
                            <th>النسبة المئوية</th>
                            <th>التقدير</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="grades-table">
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
    let deleteId = null;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    // Load all grades
    async function loadGrades() {
        try {
            const response = await axios.get('/grades');
            displayGrades(response.data.data);
        } catch (error) {
            console.error('Error loading grades:', error);
            document.getElementById('grades-table').innerHTML = `
                <tr><td colspan="9" class="text-center text-danger">حدث خطأ في تحميل البيانات</td></tr>
            `;
        }
    }

    // Load students for filter
    async function loadStudents() {
        try {
            const response = await axios.get('/students');
            const select = document.getElementById('student-filter');
            response.data.data.forEach(student => {
                const option = document.createElement('option');
                option.value = student.id;
                option.textContent = student.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading students:', error);
        }
    }

    // Load subjects for filter
    async function loadSubjects() {
        try {
            const response = await axios.get('/subjects');
            const select = document.getElementById('subject-filter');
            response.data.data.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = subject.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading subjects:', error);
        }
    }

    // Search grades
    async function searchGrades() {
        const studentId = document.getElementById('student-filter').value;
        const subjectId = document.getElementById('subject-filter').value;
        const semester = document.getElementById('semester-filter').value;
        const minGrade = document.getElementById('min-grade').value;

        try {
            const params = new URLSearchParams();
            if (studentId) params.append('student_id', studentId);
            if (subjectId) params.append('subject_id', subjectId);
            if (semester) params.append('semester', semester);
            if (minGrade) params.append('min_grade', minGrade);

            const response = await axios.get(`/grades/search?${params.toString()}`);
            displayGrades(response.data.data);
        } catch (error) {
            console.error('Error searching grades:', error);
        }
    }

    // Display grades in table
    function displayGrades(grades) {
        const tbody = document.getElementById('grades-table');

        if (grades.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center">لا توجد بيانات</td></tr>';
            return;
        }

        tbody.innerHTML = grades.map(grade => {
            const percentage = (parseFloat(grade.grade) / parseFloat(grade.max_grade)) * 100;

            return `
                <tr>
                    <td>${grade.id}</td>
                    <td>
                        <a href="/students/${grade.student_id}" class="text-decoration-none">
                            ${grade.student?.name || 'N/A'}
                        </a>
                    </td>
                    <td>
                        <a href="/subjects/${grade.subject_id}" class="text-decoration-none">
                            ${grade.subject?.name || 'N/A'}
                        </a>
                    </td>
                    <td>
                        <span class="badge bg-info">${getSemesterLabel(grade.semester)}</span>
                    </td>
                    <td><strong>${grade.grade}</strong></td>
                    <td>${grade.max_grade}</td>
                    <td>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-${getGradeColor(percentage)}"
                                 role="progressbar"
                                 style="width: ${percentage}%"
                                 aria-valuenow="${percentage}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                                ${percentage.toFixed(1)}%
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-${getGradeColor(percentage)}">
                            ${getGradeLabel(percentage)}
                        </span>
                    </td>
                    <td>
                        <a href="/grades/${grade.id}" class="btn btn-sm btn-info" title="عرض">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/grades/${grade.id}/edit" class="btn btn-sm btn-warning" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deleteGrade(${grade.id})" class="btn btn-sm btn-danger" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Get semester label
    function getSemesterLabel(semester) {
        const labels = {
            'first': 'الأول',
            'second': 'الثاني'
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
    function deleteGrade(id) {
        deleteId = id;
        deleteModal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        if (!deleteId) return;

        try {
            await axios.delete(`/grades/${deleteId}`);
            showAlert('تم حذف الدرجة بنجاح', 'success');
            deleteModal.hide();
            loadGrades();
        } catch (error) {
            console.error('Error deleting grade:', error);
        }
    }

    // Load data on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadGrades();
        loadStudents();
        loadSubjects();
    });
</script>
@endpush
