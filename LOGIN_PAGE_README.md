# صفحة تسجيل الدخول - نظام المنار

## 📁 هيكل الملفات

### ملفات Blade
- `resources/views/layouts/app.blade.php` - Layout الأساسي للمشروع
- `resources/views/auth/login.blade.php` - صفحة تسجيل الدخول الرئيسية

### ملفات CSS
- `public/css/custom.css` - الأنماط المخصصة (Glassmorphism, Animations, etc.)
- `resources/css/app.css` - ملف TailwindCSS الرئيسي

### ملفات JavaScript
- `public/js/custom.js` - الوظائف المخصصة (Toggle Password, Toast Notifications)

### ملفات التكوين
- `tailwind.config.js` - تكوين TailwindCSS
- `vite.config.js` - تكوين Vite

### Controllers
- `app/Http/Controllers/Auth/LoginController.php` - Controller لعرض صفحة تسجيل الدخول

### Routes
- `routes/web.php` - Routes للمصادقة

## 🚀 الاستخدام

### 1. تشغيل المشروع
```bash
# تطوير CSS و JS
npm run dev

# في terminal آخر
php artisan serve
```

### 2. الوصول للصفحة
افتح المتصفح على: `http://127.0.0.1:8000/login`

## 🎨 المميزات

### التصميم
- ✅ Glassmorphism Effect
- ✅ Gradient Background
- ✅ RTL Support (دعم اللغة العربية)
- ✅ Responsive Design
- ✅ Font Awesome Icons
- ✅ TailwindCSS

### الوظائف
- ✅ إظهار/إخفاء كلمة المرور
- ✅ Toast Notifications
- ✅ Validation Errors Display
- ✅ Remember Me
- ✅ Forgot Password Link

## 📝 ملاحظات

### الملفات المنفصلة
تم فصل جميع الأنماط والجافا سكريبت في ملفات منفصلة:
- **CSS**: `public/css/custom.css`
- **JavaScript**: `public/js/custom.js`

### الخطوط
- استخدام خط **Cairo** من Google Fonts

### الألوان
- اللون الأساسي: `#1db8f8` (Cyan/Turquoise)

## 🔧 التخصيص

### تغيير الألوان
قم بتعديل الألوان في:
- `public/css/custom.css`
- `tailwind.config.js`

### إضافة وظائف جديدة
أضف الكود في:
- `public/js/custom.js` للجافا سكريبت
- `public/css/custom.css` للأنماط

## 📱 التوافق
- ✅ Desktop
- ✅ Tablet
- ✅ Mobile
