@extends('layouts.app')

@section('title', 'تعديل بيانات الفصل')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2">تعديل بيانات الفصل</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('classrooms.index') }}">الفصول</a></li>
                    <li class="breadcrumb-item active">تعديل</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="classroom-form">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">اسم الفصل <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">الصف <span class="text-danger">*</span></label>
                        <input type="number" name="grade_level" class="form-control" min="1" max="12" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">الموقع</label>
                        <input type="text" name="location" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">السعة <span class="text-danger">*</span></label>
                        <input type="number" name="capacity" class="form-control" min="1" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">المعلم المسؤول</label>
                        <select name="teacher_id" class="form-select">
                            <option value="">اختر المعلم</option>
                        </select>
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
    const classroomId = window.location.pathname.split('/').filter(Boolean)[1];

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

    // Load classroom data
    async function loadClassroom() {
        try {
            const response = await axios.get(`/classrooms/${classroomId}`);
            const classroom = response.data.data;

            // Fill form with classroom data
            document.querySelector('[name="name"]').value = classroom.name;
            document.querySelector('[name="grade_level"]').value = classroom.grade_level;
            document.querySelector('[name="location"]').value = classroom.location || '';
            document.querySelector('[name="capacity"]').value = classroom.capacity;

            // Wait for teachers to load before setting teacher_id
            await loadTeachers();
            if (classroom.teacher_id) {
                document.querySelector('[name="teacher_id"]').value = classroom.teacher_id;
            }

            // Update cancel button link
            document.getElementById('cancel-btn').href = `/classrooms/${classroom.id}`;
        } catch (error) {
            console.error('Error loading classroom:', error);
            showAlert('حدث خطأ في تحميل البيانات', 'danger');
        }
    }

    // Submit form
    document.getElementById('classroom-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        // Remove empty teacher_id
        if (!data.teacher_id) delete data.teacher_id;

        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        try {
            const response = await axios.put(`/classrooms/${classroomId}`, data);
            showAlert(response.data.message, 'success');
            setTimeout(() => {
                window.location.href = `/classrooms/${classroomId}`;
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

    // Load classroom data on page load
    document.addEventListener('DOMContentLoaded', loadClassroom);
</script>
@endpush
