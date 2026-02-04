# تعليمات بناء الـ Assets على السيرفر

## المشكلة
الألوان والـ styles غير واضحة لأن ملفات الـ build غير موجودة على السيرفر.

## الحل

### 1. الانتقال لمجلد المشروع
```bash
cd /path/to/your/project
# أو
cd ~/public_html
```

### 2. تثبيت المكتبات (Node.js)
```bash
npm install
```

### 3. بناء الـ Assets
```bash
npm run build
```

### 4. التأكد من الصلاحيات
```bash
chmod -R 755 public/build
chown -R www-data:www-data public/build
```

### 5. التحقق من النتيجة
```bash
ls -la public/build/
# يجب أن ترى ملف manifest.json
```

## ملاحظات مهمة

1. **تأكد من وجود Node.js على السيرفر:**
   ```bash
   node --version
   npm --version
   ```

2. **إذا لم يكن Node.js مثبت:**
   - على cPanel: استخدم Node.js Selector
   - على VPS: `sudo apt install nodejs npm` (Ubuntu/Debian)

3. **بعد كل تحديث للكود:**
   ```bash
   npm run build
   ```

4. **للتحقق من أن النظام يستخدم الملفات المبنية:**
   - افتح صفحة الموقع
   - View Source
   - ابحث عن `/build/assets/` في الـ HTML
   - إذا وجدتها = النظام يستخدم الملفات المبنية ✅
   - إذا لم تجدها = النظام يستخدم CDN ❌

## استكشاف الأخطاء

### خطأ: "command not found: npm"
```bash
# تثبيت Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### خطأ: "Permission denied"
```bash
sudo chown -R $USER:$USER .
npm install
npm run build
```

### خطأ: "ENOSPC: System limit for number of file watchers reached"
```bash
echo fs.inotify.max_user_watches=524288 | sudo tee -a /etc/sysctl.conf
sudo sysctl -p
```
