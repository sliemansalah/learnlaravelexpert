# 📚 توثيق School Management API

## نظرة عامة
هذا API لإدارة نظام مدرسة يشمل المعلمين، الطلاب، الفصول، المواد، والدرجات.

**Base URL:** `http://localhost:8000/api`

---

## 📋 جدول المحتويات
1. [Teachers API](#teachers-api)
2. [Students API](#students-api)
3. [Classrooms API](#classrooms-api)
4. [Subjects API](#subjects-api)
5. [Grades API](#grades-api)

---

## 🧑‍🏫 Teachers API

### 1. الحصول على جميع المعلمين
```http
GET /api/teachers
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "أحمد محمد",
      "email": "ahmad@example.com",
      "phone": "0123456789",
      "specialization": "رياضيات",
      "hire_date": "2023-01-15",
      "salary": "5000.00",
      "status": "active",
      "classroom": {...},
      "subjects": [...],
      "subjects_count": 2
    }
  ]
}
```

### 2. إضافة معلم جديد
```http
POST /api/teachers
```

**Request Body:**
```json
{
  "name": "أحمد محمد",
  "email": "ahmad@example.com",
  "phone": "0123456789",
  "specialization": "رياضيات",
  "hire_date": "2023-01-15",
  "salary": 5000,
  "status": "active"
}
```

**Validation Rules:**
- `name`: مطلوب، نص، حد أقصى 255 حرف
- `email`: مطلوب، بريد إلكتروني صحيح، فريد
- `phone`: اختياري، نص، حد أقصى 20 حرف
- `specialization`: مطلوب، نص، حد أقصى 100 حرف
- `hire_date`: مطلوب، تاريخ
- `salary`: مطلوب، رقم، أكبر من أو يساوي 0
- `status`: مطلوب، أحد القيم: `active`, `inactive`, `on_leave`

### 3. عرض معلم محدد
```http
GET /api/teachers/{id}
```

### 4. تحديث معلم
```http
PUT /api/teachers/{id}
PATCH /api/teachers/{id}
```

### 5. حذف معلم
```http
DELETE /api/teachers/{id}
```

### 6. الحصول على مواد المعلم
```http
GET /api/teachers/{id}/subjects
```

### 7. البحث عن المعلمين
```http
GET /api/teachers/search?query=أحمد&status=active&specialization=رياضيات
```

**Query Parameters:**
- `query`: البحث في الاسم، البريد الإلكتروني، أو التخصص
- `status`: تصفية حسب الحالة
- `specialization`: تصفية حسب التخصص

---

## 🎓 Students API

### 1. الحصول على جميع الطلاب
```http
GET /api/students
```

### 2. إضافة طالب جديد
```http
POST /api/students
```

**Request Body:**
```json
{
  "name": "محمد علي",
  "email": "mohamed@example.com",
  "phone": "0123456789",
  "birth_date": "2010-05-15",
  "gender": "male",
  "address": "القاهرة، مصر",
  "guardian_name": "علي محمود",
  "guardian_phone": "0198765432",
  "enrollment_date": "2023-09-01",
  "status": "active",
  "classroom_id": 1
}
```

**Validation Rules:**
- `name`: مطلوب، نص، حد أقصى 255 حرف
- `email`: مطلوب، بريد إلكتروني صحيح، فريد
- `phone`: اختياري، نص، حد أقصى 20 حرف
- `birth_date`: مطلوب، تاريخ
- `gender`: مطلوب، أحد القيم: `male`, `female`
- `address`: اختياري، نص
- `guardian_name`: مطلوب، نص، حد أقصى 255 حرف
- `guardian_phone`: مطلوب، نص، حد أقصى 20 حرف
- `enrollment_date`: مطلوب، تاريخ
- `status`: مطلوب، أحد القيم: `active`, `inactive`, `graduated`, `transferred`
- `classroom_id`: مطلوب، يجب أن يكون معرف فصل موجود

### 3. عرض طالب محدد
```http
GET /api/students/{id}
```

### 4. تحديث طالب
```http
PUT /api/students/{id}
PATCH /api/students/{id}
```

### 5. حذف طالب
```http
DELETE /api/students/{id}
```

### 6. الحصول على درجات الطالب
```http
GET /api/students/{id}/grades
```

### 7. البحث عن الطلاب
```http
GET /api/students/search?query=محمد&classroom_id=1&status=active&gender=male
```

### 8. نقل طالب إلى فصل آخر
```http
POST /api/students/{id}/transfer
```

**Request Body:**
```json
{
  "classroom_id": 2
}
```

---

## 🏫 Classrooms API

### 1. الحصول على جميع الفصول
```http
GET /api/classrooms
```

### 2. إضافة فصل جديد
```http
POST /api/classrooms
```

**Request Body:**
```json
{
  "name": "الفصل 1-أ",
  "grade_level": "الصف الأول",
  "capacity": 30,
  "room_number": "101",
  "teacher_id": 1
}
```

**Validation Rules:**
- `name`: مطلوب، نص، حد أقصى 100 حرف
- `grade_level`: مطلوب، نص، حد أقصى 50 حرف
- `capacity`: مطلوب، عدد صحيح، أكبر من أو يساوي 1
- `room_number`: مطلوب، نص، حد أقصى 20 حرف
- `teacher_id`: اختياري، يجب أن يكون معرف معلم موجود، فريد

### 3. عرض فصل محدد
```http
GET /api/classrooms/{id}
```

### 4. تحديث فصل
```http
PUT /api/classrooms/{id}
PATCH /api/classrooms/{id}
```

### 5. حذف فصل
```http
DELETE /api/classrooms/{id}
```

**ملاحظة:** لا يمكن حذف فصل يحتوي على طلاب

### 6. الحصول على طلاب الفصل
```http
GET /api/classrooms/{id}/students
```

### 7. البحث عن الفصول
```http
GET /api/classrooms/search?query=الفصل&grade_level=الصف الأول&teacher_id=1
```

### 8. تعيين معلم للفصل
```http
POST /api/classrooms/{id}/assign-teacher
```

**Request Body:**
```json
{
  "teacher_id": 1
}
```

---

## 📖 Subjects API

### 1. الحصول على جميع المواد
```http
GET /api/subjects
```

### 2. إضافة مادة جديدة
```http
POST /api/subjects
```

**Request Body:**
```json
{
  "name": "الرياضيات",
  "code": "MATH101",
  "description": "مادة الرياضيات للصف الأول",
  "credit_hours": 3,
  "type": "theoretical",
  "teacher_id": 1
}
```

**Validation Rules:**
- `name`: مطلوب، نص، حد أقصى 100 حرف
- `code`: مطلوب، نص، حد أقصى 20 حرف، فريد
- `description`: اختياري، نص
- `credit_hours`: مطلوب، عدد صحيح، أكبر من أو يساوي 1
- `type`: مطلوب، أحد القيم: `theoretical`, `practical`, `combined`
- `teacher_id`: اختياري، يجب أن يكون معرف معلم موجود

### 3. عرض مادة محددة
```http
GET /api/subjects/{id}
```

### 4. تحديث مادة
```http
PUT /api/subjects/{id}
PATCH /api/subjects/{id}
```

### 5. حذف مادة
```http
DELETE /api/subjects/{id}
```

### 6. الحصول على الطلاب المسجلين في المادة
```http
GET /api/subjects/{id}/students
```

### 7. الحصول على درجات المادة
```http
GET /api/subjects/{id}/grades
```

### 8. البحث عن المواد
```http
GET /api/subjects/search?query=رياضيات&type=theoretical&teacher_id=1&credit_hours=3
```

### 9. تعيين معلم للمادة
```http
POST /api/subjects/{id}/assign-teacher
```

**Request Body:**
```json
{
  "teacher_id": 1
}
```

---

## 📊 Grades API

### 1. الحصول على جميع الدرجات
```http
GET /api/grades
```

### 2. إضافة درجة جديدة
```http
POST /api/grades
```

**Request Body:**
```json
{
  "student_id": 1,
  "subject_id": 1,
  "score": 85.5,
  "semester": "first",
  "exam_type": "final",
  "notes": "أداء ممتاز"
}
```

**Validation Rules:**
- `student_id`: مطلوب، يجب أن يكون معرف طالب موجود
- `subject_id`: مطلوب، يجب أن يكون معرف مادة موجودة
- `score`: مطلوب، رقم، بين 0 و 100
- `semester`: مطلوب، أحد القيم: `first`, `second`
- `exam_type`: مطلوب، أحد القيم: `midterm`, `final`, `quiz`, `assignment`
- `notes`: اختياري، نص

### 3. عرض درجة محددة
```http
GET /api/grades/{id}
```

### 4. تحديث درجة
```http
PUT /api/grades/{id}
PATCH /api/grades/{id}
```

### 5. حذف درجة
```http
DELETE /api/grades/{id}
```

### 6. الحصول على درجات طالب محدد
```http
GET /api/grades/student/{student_id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "grades": [...],
    "statistics": {
      "average": 85.5,
      "count": 5,
      "passing_count": 5,
      "failing_count": 0
    }
  }
}
```

### 7. الحصول على درجات مادة محددة
```http
GET /api/grades/subject/{subject_id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "grades": [...],
    "statistics": {
      "average": 82.3,
      "highest": 95.0,
      "lowest": 65.0,
      "count": 10,
      "passing_count": 9,
      "failing_count": 1
    }
  }
}
```

### 8. البحث عن الدرجات
```http
GET /api/grades/search?student_id=1&subject_id=1&semester=first&exam_type=final&min_score=60&max_score=100
```

### 9. تقرير الدرجات حسب الفصل الدراسي
```http
GET /api/grades/report/semester/{semester}
```

**Parameters:**
- `semester`: `first` أو `second`

**Response:**
```json
{
  "success": true,
  "data": {
    "semester": "first",
    "statistics": {
      "total_grades": 100,
      "average_score": 78.5,
      "highest_score": 98.0,
      "lowest_score": 45.0,
      "passing_count": 85,
      "failing_count": 15,
      "grade_distribution": {
        "A": 20,
        "B": 30,
        "C": 25,
        "D": 10,
        "F": 15
      }
    }
  }
}
```

---

## 🏥 Health Check

### فحص حالة API
```http
GET /api/health
```

**Response:**
```json
{
  "success": true,
  "message": "API is working!",
  "version": "1.0.0",
  "timestamp": "2024-11-26T12:00:00.000000Z"
}
```

---

## 📝 معايير الاستجابة

### الاستجابة الناجحة
```json
{
  "success": true,
  "data": {...},
  "message": "رسالة نجاح" // اختياري
}
```

### الاستجابة بخطأ التحقق من الصحة (422)
```json
{
  "success": false,
  "errors": {
    "email": ["The email field is required."],
    "name": ["The name must not exceed 255 characters."]
  }
}
```

### الاستجابة بخطأ Not Found (404)
```json
{
  "success": false,
  "message": "المورد غير موجود"
}
```

### الاستجابة بخطأ Bad Request (400)
```json
{
  "success": false,
  "message": "وصف الخطأ"
}
```

---

## 🔍 أمثلة على الاستخدام

### مثال 1: إضافة معلم وتعيينه لفصل
```bash
# 1. إضافة المعلم
curl -X POST http://localhost:8000/api/teachers \
  -H "Content-Type: application/json" \
  -d '{
    "name": "أحمد محمد",
    "email": "ahmad@example.com",
    "specialization": "رياضيات",
    "hire_date": "2024-01-01",
    "salary": 5000,
    "status": "active"
  }'

# 2. تعيين المعلم للفصل
curl -X POST http://localhost:8000/api/classrooms/1/assign-teacher \
  -H "Content-Type: application/json" \
  -d '{"teacher_id": 1}'
```

### مثال 2: إضافة طالب وتسجيل درجاته
```bash
# 1. إضافة الطالب
curl -X POST http://localhost:8000/api/students \
  -H "Content-Type: application/json" \
  -d '{
    "name": "محمد علي",
    "email": "mohamed@example.com",
    "birth_date": "2010-05-15",
    "gender": "male",
    "guardian_name": "علي محمود",
    "guardian_phone": "0198765432",
    "enrollment_date": "2024-09-01",
    "status": "active",
    "classroom_id": 1
  }'

# 2. إضافة درجة
curl -X POST http://localhost:8000/api/grades \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 1,
    "subject_id": 1,
    "score": 85,
    "semester": "first",
    "exam_type": "final"
  }'

# 3. الحصول على درجات الطالب
curl http://localhost:8000/api/students/1/grades
```

---

## 💡 ملاحظات مهمة

1. **Soft Deletes**: المعلمين، الطلاب، الفصول، والمواد تستخدم Soft Deletes، مما يعني أنها لا تُحذف فعلياً من قاعدة البيانات
2. **Unique Constraints**:
   - البريد الإلكتروني للمعلمين والطلاب يجب أن يكون فريداً
   - كود المادة يجب أن يكون فريداً
   - كل فصل يمكن أن يكون له معلم واحد فقط كرئيس فصل
3. **Relationships**: تأكد من وجود السجلات المرتبطة قبل إنشاء علاقات جديدة
4. **GPA Calculation**: يتم حساب المعدل التراكمي تلقائياً عند طلب بيانات الطالب
5. **Letter Grades**: التقديرات بالحروف يتم حسابها بناءً على النسبة المئوية:
   - A: 90-100
   - B: 80-89
   - C: 70-79
   - D: 60-69
   - F: أقل من 60

---

## 🚀 البدء

1. تأكد من تشغيل الخادم:
```bash
php artisan serve
```

2. اختبر الـ API:
```bash
curl http://localhost:8000/api/health
```

3. ابدأ في استخدام الـ endpoints حسب حاجتك!
