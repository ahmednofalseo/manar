# تعليمات التشغيل بعد التعديلات
## Deployment Instructions - Foundation Fixes

**التاريخ:** {{ date('Y-m-d H:i:s') }}

---

## 📋 ملخص التغييرات

تم تطبيق الإصلاحات التالية على أساس النظام:

### ✅ Commit 1: فصل land_number عن land_code
- إضافة حقل `land_code` منفصل في جدول `projects`
- تحديث Model, Controller, Views

### ✅ Commit 2: إضافة practice_license_file
- إضافة حقل `practice_license_file` في جدول `users`
- تحديث Model, Controller, Views
- إضافة validation للرفع

### ✅ Commit 3: إضافة المرحلة السابعة
- إضافة مرحلة "صحي/بيئي" كالمرحلة السابعة
- تحديث UI في صفحات Create/Edit للمشاريع

---

## 🚀 خطوات التشغيل

### 1. تشغيل Migrations

```bash
php artisan migrate
```

**الملفات الجديدة:**
- `database/migrations/2026_01_06_052656_add_land_code_to_projects_table.php`
- `database/migrations/2026_01_06_052657_add_practice_license_file_to_users_table.php`

### 2. ربط Storage (إذا لم يكن موجود)

```bash
php artisan storage:link
```

**ملاحظة:** ملفات `practice_license_file` ستُخزن في:
- `storage/app/public/practice-licenses/`

### 3. مسح Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 4. (اختياري) إعادة Seed للأدوار

إذا كنت تريد إعادة تعيين الأدوار والصلاحيات:

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

**تحذير:** هذا سيحذف الأدوار والصلاحيات الحالية ويستبدلها!

---

## ⚠️ ملاحظات مهمة

### Backward Compatibility

1. **land_number**: البيانات القديمة ستبقى كما هي في حقل `land_number`
2. **land_code**: الحقل الجديد سيكون `null` للبيانات القديمة (يمكن ملؤه يدوياً)
3. **practice_license_file**: الحقل الجديد سيكون `null` للبيانات القديمة

### File Storage

- ملفات `practice_license_file` ستُخزن في: `storage/app/public/practice-licenses/`
- تأكد من وجود المجلد أو سيتم إنشاؤه تلقائياً

### Validation

- جميع الـvalidation rules محدثة
- `practice_license_file`: يقبل PDF, JPG, JPEG, PNG (حد أقصى 5MB)
- `land_code`: حقل نصي اختياري

---

## 🧪 اختبار التغييرات

### 1. اختبار Projects

1. افتح `/projects/create`
2. تحقق من وجود حقلين منفصلين: "رقم الأرض" و "كود الأرض"
3. تحقق من وجود 7 مراحل (معماري، إنشائي، كهربائي، ميكانيكي، صحي/بيئي، تقديم للبلدية، أخرى)
4. أنشئ مشروع جديد واختبر الحقول

### 2. اختبار Users

1. افتح `/admin/users/create`
2. تحقق من وجود حقل رفع ملف شهادة مزاولة المهنة
3. أنشئ موظف جديد وارفع ملف
4. افتح `/admin/users/{id}/edit` وتحقق من عرض الملف المرفوع

---

## 📝 الملفات المعدلة

### Migrations
- `database/migrations/2026_01_06_052656_add_land_code_to_projects_table.php` (جديد)
- `database/migrations/2026_01_06_052657_add_practice_license_file_to_users_table.php` (جديد)

### Models
- `app/Models/Project.php` (محدث)
- `app/Models/User.php` (محدث)

### Controllers
- `app/Http/Controllers/ProjectsController.php` (محدث)
- `app/Http/Controllers/UsersController.php` (محدث)

### Requests
- `app/Http/Requests/StoreUserRequest.php` (محدث)
- `app/Http/Requests/UpdateUserRequest.php` (محدث)

### Views
- `resources/views/projects/create.blade.php` (محدث)
- `resources/views/projects/edit.blade.php` (محدث)
- `resources/views/admin/users/edit.blade.php` (محدث)

---

## 🔄 Rollback (إذا لزم الأمر)

إذا أردت التراجع عن التغييرات:

```bash
# Rollback آخر migration
php artisan migrate:rollback --step=2

# أو Rollback migration محدد
php artisan migrate:rollback --path=database/migrations/2026_01_06_052656_add_land_code_to_projects_table.php
php artisan migrate:rollback --path=database/migrations/2026_01_06_052657_add_practice_license_file_to_users_table.php
```

**تحذير:** هذا سيحذف الحقول الجديدة من قاعدة البيانات!

---

## ✅ Checklist

- [ ] تشغيل Migrations
- [ ] ربط Storage
- [ ] مسح Cache
- [ ] اختبار إنشاء مشروع جديد
- [ ] اختبار تعديل مشروع موجود
- [ ] اختبار إنشاء موظف جديد
- [ ] اختبار تعديل موظف موجود
- [ ] التحقق من رفع الملفات
- [ ] التحقق من عرض المراحل السبع

---

**تم إعداد التعليمات بواسطة:** Senior Laravel Architect  
**التاريخ:** {{ date('Y-m-d H:i:s') }}






