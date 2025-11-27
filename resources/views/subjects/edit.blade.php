@extends('layouts.app')

@section('title', 'تعديل بيانات المادة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2">تعديل بيانات المادة</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">المواد الدراسية</a></li>
                    <li class="breadcrumb-item active">تعديل</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="subject-form">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">اسم المادة <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">كود المادة <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">الساعات الأسبوعية <span class="text-danger">*</span></label>
                        <input type="number" name="weekly_hours" class="form-control" min="1" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">الفصل</label>
                        <select name="classroom_id" class="form-select">
                            <option value="">اختر الفصل</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">المعلم</label>
                        <select name="teacher_id" class="form-select">
                            <option value="">اختر المعلم</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
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
    const subjectId = window.location.pathname.split('/').filter(Boolean)[1];

    // Load classrooms
    async function loadClassrooms() {
        try {
            const response = await axios.get('/classrooms');
            const select = document.querySelector('[name="classroom_id"]');
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

    // Load teachers
    async function loadTeachers() {
        try {
            const response = await axios.get('/teachers');
            const select = document.querySelector('[name="teacher_id"]');
            response.data.data.forEach(teacher => {
                const option = document.createElement('option');
                option.value = teacher.id;
                option.textContent = `${teacher.name} - ${teacher.specialization}`;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading teachers:', error);
        }
    }

    // Load subject data
    async function loadSubject() {
        try {
            const response = await axios.get(`/subjects/${subjectId}`);
            const subject = response.data.data;

            // Fill form with subject data
            document.querySelector('[name="name"]').value = subject.name;
            document.querySelector('[name="code"]').value = subject.code;
            document.querySelector('[name="weekly_hours"]').value = subject.weekly_hours;
            document.querySelector('[name="description"]').value = subject.description || '';

            // Wait for dropdowns to load before setting values
            await loadClassrooms();
            await loadTeachers();

            if (subject.classroom_id) {
                document.querySelector('[name="classroom_id"]').value = subject.classroom_id;
            }
            if (subject.teacher_id) {
                document.querySelector('[name="teacher_id"]').value = subject.teacher_id;
            }

            // Update cancel button link
            document.getElementById('cancel-btn').href = `/subjects/${subject.id}`;
        } catch (error) {
            console.error('Error loading subject:', error);
            showAlert('حدث خطأ في تحميل البيانات', 'danger');
        }
    }

    // Submit form
    document.getElementById('subject-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        // Remove empty fields
        if (!data.classroom_id) delete data.classroom_id;
        if (!data.teacher_id) delete data.teacher_id;

        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        try {
            const response = await axios.put(`/subjects/${subjectId}`, data);
            showAlert(response.data.message, 'success');
            setTimeout(() => {
                window.location.href = `/subjects/${subjectId}`;
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

    // Load subject data on page load
    document.addEventListener('DOMContentLoaded', loadSubject);
</script>
@endpush
