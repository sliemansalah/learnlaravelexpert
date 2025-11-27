@extends('layouts.app')

@section('title', 'تعديل بيانات الطالب')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2">تعديل بيانات الطالب</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">الطلاب</a></li>
                    <li class="breadcrumb-item active">تعديل</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="student-form">
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
                        <label class="form-label">تاريخ الميلاد <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_birth" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">الجنس <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select" required>
                            <option value="">اختر الجنس</option>
                            <option value="male">ذكر</option>
                            <option value="female">أنثى</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">الصف <span class="text-danger">*</span></label>
                        <input type="number" name="grade_level" class="form-control" min="1" max="12" required>
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
                        <label class="form-label">الحالة <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                            <option value="graduated">متخرج</option>
                            <option value="suspended">موقوف</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">العنوان</label>
                        <textarea name="address" class="form-control" rows="3"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">اسم ولي الأمر</label>
                        <input type="text" name="parent_name" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">هاتف ولي الأمر</label>
                        <input type="tel" name="parent_phone" class="form-control">
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
    const studentId = window.location.pathname.split('/').filter(Boolean)[1];

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

    // Load student data
    async function loadStudent() {
        try {
            const response = await axios.get(`/students/${studentId}`);
            const student = response.data.data;

            // Fill form with student data
            document.querySelector('[name="name"]').value = student.name;
            document.querySelector('[name="email"]').value = student.email;
            document.querySelector('[name="phone"]').value = student.phone || '';
            document.querySelector('[name="date_of_birth"]').value = student.date_of_birth;
            document.querySelector('[name="gender"]').value = student.gender;
            document.querySelector('[name="grade_level"]').value = student.grade_level;
            document.querySelector('[name="status"]').value = student.status;
            document.querySelector('[name="address"]').value = student.address || '';
            document.querySelector('[name="parent_name"]').value = student.parent_name || '';
            document.querySelector('[name="parent_phone"]').value = student.parent_phone || '';

            // Wait for classrooms to load before setting classroom_id
            await loadClassrooms();
            if (student.classroom_id) {
                document.querySelector('[name="classroom_id"]').value = student.classroom_id;
            }

            // Update cancel button link
            document.getElementById('cancel-btn').href = `/students/${student.id}`;
        } catch (error) {
            console.error('Error loading student:', error);
            showAlert('حدث خطأ في تحميل البيانات', 'danger');
        }
    }

    // Submit form
    document.getElementById('student-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        // Remove empty classroom_id
        if (!data.classroom_id) delete data.classroom_id;

        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        try {
            const response = await axios.put(`/students/${studentId}`, data);
            showAlert(response.data.message, 'success');
            setTimeout(() => {
                window.location.href = `/students/${studentId}`;
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

    // Load student data on page load
    document.addEventListener('DOMContentLoaded', loadStudent);
</script>
@endpush
