@extends('layouts.app')

@section('title', 'الفصول الدراسية')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h2">الفصول الدراسية</h1>
            <p class="text-muted">إدارة الفصول الدراسية في النظام</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('classrooms.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة فصل جديد
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" id="search-query" class="form-control" placeholder="بحث بالاسم أو الموقع">
                </div>
                <div class="col-md-3">
                    <input type="number" id="grade-filter" class="form-control" placeholder="الصف" min="1" max="12">
                </div>
                <div class="col-md-3">
                    <input type="number" id="capacity-filter" class="form-control" placeholder="السعة" min="1">
                </div>
                <div class="col-md-2">
                    <button onclick="searchClassrooms()" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Classrooms Cards -->
    <div class="row" id="classrooms-container">
        <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
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
                هل أنت متأكد من حذف هذا الفصل؟
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

    // Load all classrooms
    async function loadClassrooms() {
        try {
            const response = await axios.get('/classrooms');
            displayClassrooms(response.data.data);
        } catch (error) {
            console.error('Error loading classrooms:', error);
            document.getElementById('classrooms-container').innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>
                </div>
            `;
        }
    }

    // Search classrooms
    async function searchClassrooms() {
        const query = document.getElementById('search-query').value;
        const gradeLevel = document.getElementById('grade-filter').value;
        const capacity = document.getElementById('capacity-filter').value;

        try {
            const params = new URLSearchParams();
            if (query) params.append('query', query);
            if (gradeLevel) params.append('grade_level', gradeLevel);
            if (capacity) params.append('capacity', capacity);

            const response = await axios.get(`/classrooms/search?${params.toString()}`);
            displayClassrooms(response.data.data);
        } catch (error) {
            console.error('Error searching classrooms:', error);
        }
    }

    // Display classrooms
    function displayClassrooms(classrooms) {
        const container = document.getElementById('classrooms-container');

        if (classrooms.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info">لا توجد فصول دراسية</div>
                </div>
            `;
            return;
        }

        container.innerHTML = classrooms.map(classroom => {
            const usagePercentage = classroom.capacity > 0
                ? ((classroom.students_count || 0) / classroom.capacity * 100).toFixed(0)
                : 0;
            const progressColor = usagePercentage >= 90 ? 'danger' : usagePercentage >= 70 ? 'warning' : 'success';

            return `
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-door-open text-primary"></i>
                                    ${classroom.name}
                                </h5>
                                <span class="badge bg-primary">الصف ${classroom.grade_level}</span>
                            </div>

                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt"></i>
                                ${classroom.location || 'غير محدد'}
                            </p>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">الطلاب</small>
                                    <small class="text-muted">${classroom.students_count || 0} / ${classroom.capacity}</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-${progressColor}" role="progressbar"
                                         style="width: ${usagePercentage}%"
                                         aria-valuenow="${usagePercentage}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>

                            ${classroom.teacher ? `
                                <p class="mb-3">
                                    <i class="fas fa-chalkboard-teacher text-success"></i>
                                    <small>${classroom.teacher.name}</small>
                                </p>
                            ` : '<p class="mb-3 text-muted"><small>لا يوجد معلم مسؤول</small></p>'}

                            <div class="d-flex gap-2">
                                <a href="/classrooms/${classroom.id}" class="btn btn-sm btn-info flex-fill" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/classrooms/${classroom.id}/edit" class="btn btn-sm btn-warning flex-fill" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteClassroom(${classroom.id})" class="btn btn-sm btn-danger flex-fill" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Delete classroom
    function deleteClassroom(id) {
        deleteId = id;
        deleteModal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        if (!deleteId) return;

        try {
            await axios.delete(`/classrooms/${deleteId}`);
            showAlert('تم حذف الفصل بنجاح', 'success');
            deleteModal.hide();
            loadClassrooms();
        } catch (error) {
            console.error('Error deleting classroom:', error);
        }
    }

    // Load classrooms on page load
    document.addEventListener('DOMContentLoaded', loadClassrooms);

    // Search on Enter key
    document.getElementById('search-query').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') searchClassrooms();
    });
</script>
@endpush
