<!-- User Roles Modal - إدارة الأدوار والصلاحيات -->
<div
    id="userRolesModal"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);"
    x-data="userRolesModal()"
    x-init="init()"
    x-show="isOpen"
    x-cloak
    x-transition
    @click.away="close()"
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
                    <label class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-white/10 cursor-pointer hover:bg-white/10 transition-all" @click.prevent="toggleRole('super_admin')">
                        <input type="checkbox" :checked="hasRole('super_admin')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                        <div>
                            <p class="text-white font-semibold">الأدمن العام</p>
                            <p class="text-gray-400 text-xs">صلاحيات كاملة</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-white/10 cursor-pointer hover:bg-white/10 transition-all" @click.prevent="toggleRole('project_manager')">
                        <input type="checkbox" :checked="hasRole('project_manager')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                        <div>
                            <p class="text-white font-semibold">مدير المشروع</p>
                            <p class="text-gray-400 text-xs">إدارة المشاريع والمهام</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-white/10 cursor-pointer hover:bg-white/10 transition-all" @click.prevent="toggleRole('engineer')">
                        <input type="checkbox" :checked="hasRole('engineer')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                        <div>
                            <p class="text-white font-semibold">مهندس/فني</p>
                            <p class="text-gray-400 text-xs">إدارة المهام المسندة</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-white/10 cursor-pointer hover:bg-white/10 transition-all" @click.prevent="toggleRole('admin_staff')">
                        <input type="checkbox" :checked="hasRole('admin_staff')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                        <div>
                            <p class="text-white font-semibold">الإداري</p>
                            <p class="text-gray-400 text-xs">إدارة العملاء والفواتير</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Permissions by Group -->
            <div>
                <label class="block text-gray-300 text-sm mb-3">الصلاحيات <span class="text-gray-500 text-xs">(مخصصة للمستخدم)</span></label>
                <div class="space-y-4">
                    <!-- Users Permissions -->
                    <div class="bg-white/5 rounded-lg p-4">
                        <h4 class="text-white font-semibold mb-3">المستخدمون</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('users.view')">
                                <input type="checkbox" :checked="hasPermission('users.view')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">عرض المستخدمين</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('users.create')">
                                <input type="checkbox" :checked="hasPermission('users.create')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">إنشاء مستخدم</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('users.edit')">
                                <input type="checkbox" :checked="hasPermission('users.edit')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">تعديل مستخدم</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('users.delete')">
                                <input type="checkbox" :checked="hasPermission('users.delete')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">حذف مستخدم</span>
                            </label>
                        </div>
                    </div>

                    <!-- Projects Permissions -->
                    <div class="bg-white/5 rounded-lg p-4">
                        <h4 class="text-white font-semibold mb-3">المشاريع</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('projects.view')">
                                <input type="checkbox" :checked="hasPermission('projects.view')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">عرض المشاريع</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('projects.create')">
                                <input type="checkbox" :checked="hasPermission('projects.create')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">إنشاء مشروع</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('projects.edit')">
                                <input type="checkbox" :checked="hasPermission('projects.edit')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">تعديل مشروع</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('projects.manage')">
                                <input type="checkbox" :checked="hasPermission('projects.manage')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">إدارة كاملة</span>
                            </label>
                        </div>
                    </div>

                    <!-- Tasks Permissions -->
                    <div class="bg-white/5 rounded-lg p-4">
                        <h4 class="text-white font-semibold mb-3">المهام</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('tasks.view')">
                                <input type="checkbox" :checked="hasPermission('tasks.view')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">عرض المهام</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('tasks.create')">
                                <input type="checkbox" :checked="hasPermission('tasks.create')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">إنشاء مهمة</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('tasks.manage')">
                                <input type="checkbox" :checked="hasPermission('tasks.manage')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">إدارة كاملة</span>
                            </label>
                        </div>
                    </div>

                    <!-- Financials Permissions -->
                    <div class="bg-white/5 rounded-lg p-4">
                        <h4 class="text-white font-semibold mb-3">المالية</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('financials.view')">
                                <input type="checkbox" :checked="hasPermission('financials.view')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">عرض الفواتير</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('financials.manage')">
                                <input type="checkbox" :checked="hasPermission('financials.manage')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">إدارة كاملة</span>
                            </label>
                        </div>
                    </div>

                    <!-- Settings Permissions -->
                    <div class="bg-white/5 rounded-lg p-4">
                        <h4 class="text-white font-semibold mb-3">الإعدادات</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('settings.view')">
                                <input type="checkbox" :checked="hasPermission('settings.view')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">عرض الإعدادات</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer" @click.prevent="togglePermission('settings.manage')">
                                <input type="checkbox" :checked="hasPermission('settings.manage')" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400 pointer-events-none">
                                <span class="text-gray-300 text-sm">إدارة كاملة</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                <button type="button" @click="close()" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                    إلغاء
                </button>
                <button type="submit" :disabled="saving" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50">
                    <i class="fas fa-save ml-2"></i>
                    <span x-text="saving ? 'جاري الحفظ...' : 'حفظ'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>

<script>
function userRolesModal() {
    return {
        isOpen: false,
        saving: false,
        form: { roles: [], permissions: [], userId: null },
        init() {
            window.addEventListener('open-user-roles-modal', (e) => {
                const d = e.detail || {};
                this.form.userId = d.userId;
                this.form.roles = [...(d.roleNames || [])];
                this.form.permissions = [...(d.permissionNames || [])];
                this.isOpen = true;
            });
        },
        close() {
            this.isOpen = false;
            this.form = { roles: [], permissions: [], userId: null };
        },
        hasRole(name) {
            return (this.form.roles || []).includes(name);
        },
        toggleRole(name) {
            const arr = this.form.roles || [];
            this.form.roles = arr.includes(name) ? arr.filter(r => r !== name) : [...arr, name];
        },
        hasPermission(name) {
            return (this.form.permissions || []).includes(name);
        },
        togglePermission(name) {
            const arr = this.form.permissions || [];
            this.form.permissions = arr.includes(name) ? arr.filter(p => p !== name) : [...arr, name];
        },
        async submit() {
            if (!this.form.roles?.length) {
                alert('يرجى اختيار دور واحد على الأقل');
                return;
            }
            this.saving = true;
            const url = `/admin/users/${this.form.userId}/roles`;
            const fd = new FormData();
            (this.form.roles || []).forEach(r => fd.append('roles[]', r));
            (this.form.permissions || []).forEach(p => fd.append('permissions[]', p));
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: fd
                });
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.success !== false) {
                    this.close();
                    window.location.reload();
                } else {
                    alert(data.message || data.errors?.roles?.[0] || data.errors?.permissions?.[0] || 'حدث خطأ أثناء التحديث');
                }
            } catch (err) {
                console.error(err);
                alert('حدث خطأ في الاتصال');
            } finally {
                this.saving = false;
            }
        }
    };
}
</script>
