<!-- User Roles Modal -->
<div 
    id="userRolesModal" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);"
    x-data="userRolesModal()"
    x-init="init()"
    x-show="isOpen"
    @click.away="close()"
    x-cloak
    x-transition
>
    <div class="glass-card rounded-2xl p-6 max-w-3xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">إدارة الأدوار والصلاحيات</h2>
            <button @click="close()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit()" class="space-y-6">
            <!-- Roles Selection -->
            <div>
                <label class="block text-gray-300 text-sm mb-3">الأدوار <span class="text-red-400">*</span></label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-white/10 cursor-pointer hover:bg-white/10 transition-all">
                        <input type="checkbox" x-model="form.roles" value="super_admin" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                        <div>
                            <p class="text-white font-semibold">الأدمن العام</p>
                            <p class="text-gray-400 text-xs">صلاحيات كاملة</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-white/10 cursor-pointer hover:bg-white/10 transition-all">
                        <input type="checkbox" x-model="form.roles" value="project_manager" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                        <div>
                            <p class="text-white font-semibold">مدير المشروع</p>
                            <p class="text-gray-400 text-xs">إدارة المشاريع والمهام</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-white/10 cursor-pointer hover:bg-white/10 transition-all">
                        <input type="checkbox" x-model="form.roles" value="engineer" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                        <div>
                            <p class="text-white font-semibold">مهندس/فني</p>
                            <p class="text-gray-400 text-xs">إدارة المهام المسندة</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-white/10 cursor-pointer hover:bg-white/10 transition-all">
                        <input type="checkbox" x-model="form.roles" value="admin_staff" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                        <div>
                            <p class="text-white font-semibold">الإداري</p>
                            <p class="text-gray-400 text-xs">إدارة العملاء والفواتير</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Permissions by Group -->
            <div>
                <label class="block text-gray-300 text-sm mb-3">الصلاحيات</label>
                <div class="space-y-4">
                    <!-- Users Permissions -->
                    <div class="bg-white/5 rounded-lg p-4">
                        <h4 class="text-white font-semibold mb-3">المستخدمون</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="users.view" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">عرض المستخدمين</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="users.create" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">إنشاء مستخدم</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="users.edit" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">تعديل مستخدم</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="users.delete" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">حذف مستخدم</span>
                            </label>
                        </div>
                    </div>

                    <!-- Projects Permissions -->
                    <div class="bg-white/5 rounded-lg p-4">
                        <h4 class="text-white font-semibold mb-3">المشاريع</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="projects.view" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">عرض المشاريع</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="projects.create" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">إنشاء مشروع</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="projects.edit" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">تعديل مشروع</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="projects.manage" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">إدارة كاملة</span>
                            </label>
                        </div>
                    </div>

                    <!-- Tasks Permissions -->
                    <div class="bg-white/5 rounded-lg p-4">
                        <h4 class="text-white font-semibold mb-3">المهام</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="tasks.view" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">عرض المهام</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="tasks.create" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">إنشاء مهمة</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="tasks.manage" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">إدارة كاملة</span>
                            </label>
                        </div>
                    </div>

                    <!-- Financials Permissions -->
                    <div class="bg-white/5 rounded-lg p-4">
                        <h4 class="text-white font-semibold mb-3">المالية</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="financials.view" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">عرض الفواتير</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="financials.manage" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">إدارة كاملة</span>
                            </label>
                        </div>
                    </div>

                    <!-- Settings Permissions -->
                    <div class="bg-white/5 rounded-lg p-4">
                        <h4 class="text-white font-semibold mb-3">الإعدادات</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="settings.view" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">عرض الإعدادات</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="form.permissions" value="settings.manage" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                                <span class="text-gray-300 text-sm">إدارة كاملة</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                <button 
                    type="button" 
                    @click="close()"
                    class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200"
                >
                    إلغاء
                </button>
                <button 
                    type="submit"
                    class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200"
                >
                    <i class="fas fa-save ml-2"></i>
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

<script>
function userRolesModal() {
    return {
        isOpen: false,
        form: {
            roles: [],
            permissions: [],
            userId: null
        },
        init() {
            window.addEventListener('open-user-roles-modal', (e) => {
                this.form.userId = e.detail.userId;
                // TODO: Load current roles and permissions
                this.form.roles = ['project_manager'];
                this.form.permissions = ['projects.view', 'tasks.view'];
                this.open();
            });
        },
        open() {
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
            this.form = {
                roles: [],
                permissions: [],
                userId: null
            };
        },
        submit() {
            if (this.form.roles.length === 0) {
                alert('يرجى اختيار دور واحد على الأقل');
                return;
            }

            // TODO: Submit via AJAX
            console.log('Submitting roles and permissions:', this.form);
            alert('تم تحديث الأدوار والصلاحيات بنجاح');
            this.close();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }
}
</script>


