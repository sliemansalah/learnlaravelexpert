@extends('layouts.app')

@section('title', 'تعديل الدرجة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2">تعديل الدرجة</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('grades.index') }}">الدرجات</a></li>
                    <li class="breadcrumb-item active">تعديل</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="grade-form">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">الطالب <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select" required>
                            <option value="">اختر الطالب</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">المادة <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">اختر المادة</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">الفصل الدراسي <span class="text-danger">*</span></label>
                        <select name="semester" class="form-select" required>
                            <option value="">اختر الفصل الدراسي</option>
                            <option value="first">الفصل الأول</option>
                            <option value="second">الفصل الثاني</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">الدرجة <span class="text-danger">*</span></label>
                        <input type="number" name="grade" class="form-control" step="0.01" min="0" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">الحد الأقصى للدرجة <span class="text-danger">*</span></label>
                        <input type="number" name="max_grade" class="form-control" step="0.01" min="0" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="remarks" class="form-control" rows="3"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ التعديلات
                        </button>
                        <a href="#" id="cancel-btn" class="btn btn-secondary">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const gradeId = window.location.pathname.split('/').filter(Boolean)[1];

    // Load students
    async function loadStudents() {
        try {
            const response = await axios.get('/students');
            const select = document.querySelector('[name="student_id"]');
            response.data.data.forEach(student => {
                const option = document.createElement('option');
                option.value = student.id;
                option.textContent = `${student.name} - الصف ${student.grade_level}`;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading students:', error);
        }
    }

    // Load subjects
    async function loadSubjects() {
        try {
            const response = await axios.get('/subjects');
            const select = document.querySelector('[name="subject_id"]');
            response.data.data.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = `${subject.name} (${subject.code})`;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading subjects:', error);
        }
    }

    // Load grade data
    async function loadGrade() {
        try {
            const response = await axios.get(`/grades/${gradeId}`);
            const grade = response.data.data;

            // Wait for dropdowns to load
            await loadStudents();
            await loadSubjects();

            // Fill form with grade data
            document.querySelector('[name="student_id"]').value = grade.student_id;
            document.querySelector('[name="subject_id"]').value = grade.subject_id;
            document.querySelector('[name="semester"]').value = grade.semester;
            document.querySelector('[name="grade"]').value = grade.grade;
            document.querySelector('[name="max_grade"]').value = grade.max_grade;
            document.querySelector('[name="remarks"]').value = grade.remarks || '';

            // Update cancel button link
            document.getElementById('cancel-btn').href = `/grades/${grade.id}`;
        } catch (error) {
            console.error('Error loading grade:', error);
            showAlert('حدث خطأ في تحميل البيانات', 'danger');
        }
    }

    // Submit form
    document.getElementById('grade-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        try {
            const response = await axios.put(`/grades/${gradeId}`, data);
            showAlert(response.data.message, 'success');
            setTimeout(() => {
                window.location.href = `/grades/${gradeId}`;
            }, 1500);
        } catch (error) {
            if (error.response?.data?.errors) {
                const errors = error.response.data.errors;
                Object.keys(errors).forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        input.nextElementSibling.textContent = errors[field][0];
                    }
                });
            }
        }
    });

    // Load grade data on page load
    document.addEventListener('DOMContentLoaded', loadGrade);
</script>
@endpush
