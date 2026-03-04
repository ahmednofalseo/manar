# تقرير إصلاحات الأساس - نظام المنار
## Foundation Audit & Fix Report

**التاريخ:** {{ date('Y-m-d H:i:s') }}  
**المهندس:** Senior Laravel Architect

---

## 📋 ملخص التغييرات

### ✅ ما هو موجود:
1. **User Model** - يحتوي على حقول الموظف الأساسية (national_id, job_title, practice_license_no, engineer_rank_expiry)
2. **RBAC System** - Roles & Permissions موجودة مع 4 أدوار (super_admin, project_manager, engineer, admin_staff)
3. **Projects** - نظام المشاريع كامل مع المراحل (6 مراحل حالياً)
4. **Tasks** - نظام المهام كامل
5. **Migrations** - معظم الجداول موجودة

### ❌ ما هو ناقص/يحتاج إصلاح:
1. **فصل land_number عن land_code** - حالياً حقل واحد فقط
2. **practice_license_file** - لا يوجد حقل لرفع ملف الشهادة
3. **المرحلة السابعة** - ناقصة (6 مراحل فقط)
4. **Validation** - بعض الـForms تحتاج تحديث

---

## 🔧 التغييرات المطلوبة

### Commit 1: فصل land_number عن land_code في Projects
**الملفات:**
- `database/migrations/YYYY_MM_DD_HHMMSS_add_land_code_to_projects_table.php`
- `app/Models/Project.php`
- `app/Http/Controllers/ProjectsController.php`
- `resources/views/projects/create.blade.php`
- `resources/views/projects/edit.blade.php`

**التغييرات:**
- إضافة حقل `land_code` منفصل عن `land_number`
- تحديث Model, Controller, Views

---

### Commit 2: إضافة practice_license_file للموظف
**الملفات:**
- `database/migrations/YYYY_MM_DD_HHMMSS_add_practice_license_file_to_users_table.php`
- `app/Models/User.php`
- `app/Http/Controllers/UsersController.php`
- `resources/views/admin/users/create.blade.php`
- `resources/views/admin/users/edit.blade.php`

**التغييرات:**
- إضافة حقل `practice_license_file` في جدول users
- تحديث Model, Controller, Views
- إضافة validation للرفع

---

### Commit 3: إضافة المرحلة السابعة (صحي/بيئي)
**الملفات:**
- `resources/views/projects/create.blade.php`
- `resources/views/projects/edit.blade.php`

**التغييرات:**
- إضافة checkbox للمرحلة السابعة "صحي/بيئي"
- تحديث UI في صفحات Create/Edit

---

### Commit 4: تحديث Validation والForms
**الملفات:**
- `app/Http/Controllers/ProjectsController.php`
- `app/Http/Controllers/UsersController.php`

**التغييرات:**
- تحديث validation rules
- إضافة validation للـfiles

---

## 📝 خطوات التشغيل بعد التعديل

```bash
# 1. تشغيل Migrations
php artisan migrate

# 2. ربط Storage (إذا لم يكن موجود)
php artisan storage:link

# 3. مسح Cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 4. (اختياري) إعادة Seed للأدوار
php artisan db:seed --class=RolesAndPermissionsSeeder
```

---

## ⚠️ ملاحظات مهمة

1. **لا تغيير Routes** - جميع الـroutes الحالية ستبقى كما هي
2. **Backward Compatibility** - البيانات القديمة ستظل تعمل (land_number سيتم نسخها لـland_code إذا لزم الأمر)
3. **File Storage** - ملفات practice_license_file ستُخزن في `storage/app/public/practice-licenses/`
4. **Validation** - جميع الـvalidation rules محدثة

---

## 🎯 النتيجة المتوقعة

بعد تطبيق التغييرات:
- ✅ Projects: land_number و land_code منفصلين
- ✅ Users: يمكن رفع ملف شهادة مزاولة المهنة
- ✅ Projects: 7 مراحل متاحة (معماري، إنشائي، كهربائي، ميكانيكي، صحي/بيئي، تقديم للبلدية، أخرى)
- ✅ RBAC: 4 أدوار موجودة ومكتملة
- ✅ جميع الـroutes الحالية تعمل بدون تغيير






