# Laravel API Interfaces Documentation

## نظرة عامة

تم إنشاء واجهات (Interfaces) لجميع الـ API Controllers في المشروع لتحسين التنظيم وتسهيل الصيانة والاختبار.

## البنية الهيكلية

```
app/
├── Http/
│   └── Controllers/
│       └── Api/
│           ├── Interfaces/
│           │   ├── TeacherControllerInterface.php
│           │   ├── StudentControllerInterface.php
│           │   ├── ClassroomControllerInterface.php
│           │   ├── SubjectControllerInterface.php
│           │   └── GradeControllerInterface.php
│           ├── TeacherController.php
│           ├── StudentController.php
│           ├── ClassroomController.php
│           ├── SubjectController.php
│           └── GradeController.php
└── Providers/
    └── ControllerServiceProvider.php
```

## الواجهات المتاحة

### 1. TeacherControllerInterface
يحتوي على Methods الخاصة بإدارة المعلمين:
- `index()` - عرض قائمة المعلمين
- `store()` - إضافة معلم جديد
- `show()` - عرض معلم محدد
- `update()` - تحديث بيانات معلم
- `destroy()` - حذف معلم
- `search()` - البحث عن المعلمين
- `subjects()` - عرض المواد التي يدرسها المعلم

### 2. StudentControllerInterface
يحتوي على Methods الخاصة بإدارة الطلاب:
- `index()` - عرض قائمة الطلاب
- `store()` - إضافة طالب جديد
- `show()` - عرض طالب محدد
- `update()` - تحديث بيانات طالب
- `destroy()` - حذف طالب
- `search()` - البحث عن الطلاب
- `grades()` - عرض درجات الطالب
- `transfer()` - نقل الطالب لفصل آخر

### 3. ClassroomControllerInterface
يحتوي على Methods الخاصة بإدارة الفصول:
- `index()` - عرض قائمة الفصول
- `store()` - إضافة فصل جديد
- `show()` - عرض فصل محدد
- `update()` - تحديث بيانات فصل
- `destroy()` - حذف فصل
- `search()` - البحث عن الفصول
- `students()` - عرض طلاب الفصل
- `assignTeacher()` - تعيين معلم للفصل

### 4. SubjectControllerInterface
يحتوي على Methods الخاصة بإدارة المواد الدراسية:
- `index()` - عرض قائمة المواد
- `store()` - إضافة مادة جديدة
- `show()` - عرض مادة محددة
- `update()` - تحديث بيانات مادة
- `destroy()` - حذف مادة
- `search()` - البحث عن المواد
- `students()` - عرض الطلاب المسجلين في المادة
- `grades()` - عرض درجات المادة
- `assignTeacher()` - تعيين معلم للمادة

### 5. GradeControllerInterface
يحتوي على Methods الخاصة بإدارة الدرجات:
- `index()` - عرض قائمة الدرجات
- `store()` - إضافة درجة جديدة
- `show()` - عرض درجة محددة
- `update()` - تحديث درجة
- `destroy()` - حذف درجة
- `search()` - البحث عن الدرجات
- `studentGrades()` - عرض جميع درجات طالب محدد
- `subjectGrades()` - عرض جميع درجات مادة محددة
- `semesterReport()` - إنشاء تقرير الفصل الدراسي

## Service Provider

### ControllerServiceProvider
يقوم بربط الواجهات مع الـ Implementations:

```php
public function register(): void
{
    $this->app->bind(TeacherControllerInterface::class, TeacherController::class);
    $this->app->bind(StudentControllerInterface::class, StudentController::class);
    $this->app->bind(ClassroomControllerInterface::class, ClassroomController::class);
    $this->app->bind(SubjectControllerInterface::class, SubjectController::class);
    $this->app->bind(GradeControllerInterface::class, GradeController::class);
}
```

تم تسجيل الـ Service Provider في `bootstrap/providers.php`.

## الفوائد

### 1. تحسين قابلية الاختبار
يمكنك الآن إنشاء Mock Objects بسهولة للاختبارات:

```php
$mock = Mockery::mock(TeacherControllerInterface::class);
$this->app->instance(TeacherControllerInterface::class, $mock);
```

### 2. Dependency Injection
يمكنك استخدام الواجهات في Constructor Injection:

```php
public function __construct(
    private TeacherControllerInterface $teacherController,
    private StudentControllerInterface $studentController
) {}
```

### 3. تبديل Implementations بسهولة
يمكنك تغيير التطبيق في Service Provider دون تعديل الكود المستخدم للواجهة.

### 4. توثيق واضح للـ API
الواجهات توفر عقد واضح (Contract) لما يجب أن يوفره كل Controller.

### 5. تطبيق SOLID Principles
- **Dependency Inversion Principle**: الاعتماد على Abstractions وليس Implementations
- **Interface Segregation Principle**: كل واجهة محددة ومتخصصة

## كيفية الاستخدام

### استخدام عادي (كما هو حالياً)
```php
Route::get('/teachers', [TeacherController::class, 'index']);
```

### استخدام مع Dependency Injection
```php
class SomeService
{
    public function __construct(
        private TeacherControllerInterface $teacherController
    ) {}

    public function getAllTeachers()
    {
        return $this->teacherController->index();
    }
}
```

### اختبار مع Mock
```php
public function test_can_get_teachers()
{
    $mock = Mockery::mock(TeacherControllerInterface::class);
    $mock->shouldReceive('index')
         ->once()
         ->andReturn(response()->json(['data' => []]));

    $this->app->instance(TeacherControllerInterface::class, $mock);

    // Test code here
}
```

## ملاحظات مهمة

1. جميع الواجهات تتطلب إرجاع `JsonResponse`
2. الـ Methods تطابق تماماً ما هو موجود في Controllers
3. تم توثيق كل Method في الواجهة بـ DocBlock
4. الـ Service Provider مسجل تلقائياً في Laravel 11

## الصيانة المستقبلية

عند إضافة Methods جديدة:
1. أضف الـ Method في الواجهة أولاً
2. طبق الـ Method في الـ Controller
3. تأكد من أن الـ return type هو `JsonResponse`
4. قم بتوثيق الـ Method في الواجهة

---

تم إنشاء هذا التوثيق في: 2025-11-27

