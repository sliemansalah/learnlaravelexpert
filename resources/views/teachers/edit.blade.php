@extends('layouts.app')

@section('title', 'تعديل بيانات المعلم')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2">تعديل بيانات المعلم</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">المعلمين</a></li>
                    <li class="breadcrumb-item active">تعديل</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="teacher-form">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">الهاتف</label>
                        <input type="tel" name="phone" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">التخصص <span class="text-danger">*</span></label>
                        <input type="text" name="specialization" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">تاريخ التوظيف <span class="text-danger">*</span></label>
                        <input type="date" name="hire_date" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">الراتب <span class="text-danger">*</span></label>
                        <input type="number" name="salary" class="form-control" step="0.01" min="0" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">الحالة <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                            <option value="on_leave">في إجازة</option>
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
    const teacherId = window.location.pathname.split('/').filter(Boolean)[1];

    // Load teacher data
    async function loadTeacher() {
        try {
            const response = await axios.get(`/teachers/${teacherId}`);
            const teacher = response.data.data;

            // Fill form with teacher data
            document.querySelector('[name="name"]').value = teacher.name;
            document.querySelector('[name="email"]').value = teacher.email;
            document.querySelector('[name="phone"]').value = teacher.phone || '';
            document.querySelector('[name="specialization"]').value = teacher.specialization;
            document.querySelector('[name="hire_date"]').value = teacher.hire_date;
            document.querySelector('[name="salary"]').value = teacher.salary;
            document.querySelector('[name="status"]').value = teacher.status;

            // Update cancel button link
            document.getElementById('cancel-btn').href = `/teachers/${teacher.id}`;
        } catch (error) {
            console.error('Error loading teacher:', error);
            showAlert('حدث خطأ في تحميل البيانات', 'danger');
        }
    }

    // Submit form
    document.getElementById('teacher-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        try {
            const response = await axios.put(`/teachers/${teacherId}`, data);
            showAlert(response.data.message, 'success');
            setTimeout(() => {
                window.location.href = `/teachers/${teacherId}`;
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

    // Load teacher data on page load
    document.addEventListener('DOMContentLoaded', loadTeacher);
</script>
@endpush
