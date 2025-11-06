@extends('layouts.dashboard')

@section('title', 'إدارة العملاء - المنار')
@section('page-title', 'إدارة العملاء')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>
@endpush

@section('content')
<!-- Toast Notifications -->
@if(session('success'))
<div class="fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md bg-green-500 text-white animate-slide-in">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="mr-2">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md bg-red-500 text-white animate-slide-in">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="mr-2">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<!-- Header Actions -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">إدارة العملاء</h1>
    <div class="flex items-center gap-3">
        <button class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-gear ml-2"></i>
            إعدادات العرض
        </button>
        <button class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-file-export ml-2"></i>
            تصدير
        </button>
        <a href="{{ route('clients.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-user-plus ml-2"></i>
            عميل جديد
        </a>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">إجمالي العملاء</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">245</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">العملاء النشطون</p>
                <h3 class="text-2xl md:text-3xl font-bold text-green-400 mt-1 md:mt-2">198</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-check text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">غير النشطين</p>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-400 mt-1 md:mt-2">47</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-gray-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-slash text-gray-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">ملاحظات قيد المراجعة</p>
                <h3 class="text-2xl md:text-3xl font-bold text-yellow-400 mt-1 md:mt-2">12</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-comment-dots text-yellow-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6" x-data="clientFilters()">
    <div class="space-y-4">
        <!-- Search Bar -->
        <div class="relative">
            <input 
                type="text" 
                x-model="search"
                placeholder="بحث: الاسم، الهاتف، البريد الإلكتروني..." 
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pr-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
            >
            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <!-- Filters Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">المدينة</label>
                <select x-model="city" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع المدن</option>
                    <option value="الرياض">الرياض</option>
                    <option value="جدة">جدة</option>
                    <option value="الدمام">الدمام</option>
                    <option value="مكة">مكة</option>
                    <option value="المدينة">المدينة</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">الحالة</label>
                <select x-model="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع الحالات</option>
                    <option value="active">نشط</option>
                    <option value="inactive">غير نشط</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">نوع العميل</label>
                <select x-model="type" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع الأنواع</option>
                    <option value="individual">فرد</option>
                    <option value="company">شركة</option>
                    <option value="government">جهة حكومية</option>
                </select>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <button @click="applyFilters()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-filter ml-2"></i>
                تطبيق الفلاتر
            </button>
            <button @click="clearFilters()" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-times ml-2"></i>
                تفريغ
            </button>
        </div>
    </div>
</div>

<!-- Clients Table -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6" x-data="clientsData()">
    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-right border-b border-white/10">
                    <th class="text-gray-400 text-sm font-normal pb-3">الاسم الكامل</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">نوع العميل</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الجوال</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">البريد الإلكتروني</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">المدينة / الحي</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">عدد المشاريع</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الحالة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">تاريخ الإنشاء</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="client in clients" :key="client.id">
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="py-3 text-white text-sm font-semibold" x-text="client.name"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="getClientTypeText(client.type)"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="client.phone"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="client.email"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="client.city + ' / ' + client.district"></td>
                        <td class="py-3 text-white font-semibold" x-text="client.projectsCount"></td>
                        <td class="py-3">
                            <span 
                                class="px-2 py-1 rounded text-xs font-semibold"
                                :class="client.status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'"
                                x-text="client.status === 'active' ? 'نشط' : 'غير نشط'"
                            ></span>
                        </td>
                        <td class="py-3 text-gray-300 text-sm" x-text="client.createdAt"></td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <a :href="'/clients/' + client.id" class="text-primary-400 hover:text-primary-300" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a :href="'/clients/' + client.id + '/edit'" class="text-blue-400 hover:text-blue-300" title="تعديل">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button @click="openAttachmentModal(client.id)" class="text-purple-400 hover:text-purple-300" title="مرفقات">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                <button @click="openNotesModal(client.id)" class="text-yellow-400 hover:text-yellow-300" title="ملاحظات">
                                    <i class="fas fa-comments"></i>
                                </button>
                                <button @click="linkProject(client.id)" class="text-green-400 hover:text-green-300" title="ربط بمشروع">
                                    <i class="fas fa-link"></i>
                                </button>
                                <button @click="deleteClient(client.id)" class="text-red-400 hover:text-red-300" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-4">
        <template x-for="client in clients" :key="client.id">
            <div class="glass-card rounded-xl p-4 border border-white/10">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="text-white font-semibold mb-1" x-text="client.name"></h3>
                        <p class="text-gray-400 text-sm" x-text="getClientTypeText(client.type)"></p>
                    </div>
                    <span 
                        class="px-2 py-1 rounded text-xs font-semibold"
                        :class="client.status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'"
                        x-text="client.status === 'active' ? 'نشط' : 'غير نشط'"
                    ></span>
                </div>
                <div class="space-y-2 mb-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">الجوال</span>
                        <span class="text-white text-sm" x-text="client.phone"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">البريد</span>
                        <span class="text-white text-sm" x-text="client.email"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">المدينة</span>
                        <span class="text-white text-sm" x-text="client.city + ' / ' + client.district"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">المشاريع</span>
                        <span class="text-white font-semibold" x-text="client.projectsCount"></span>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-white/10">
                    <div class="flex items-center gap-2">
                        <a :href="'/clients/' + client.id" class="text-primary-400 hover:text-primary-300">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a :href="'/clients/' + client.id + '/edit'" class="text-blue-400 hover:text-blue-300">
                            <i class="fas fa-pen"></i>
                        </a>
                        <button @click="openAttachmentModal(client.id)" class="text-purple-400 hover:text-purple-300">
                            <i class="fas fa-paperclip"></i>
                        </button>
                    </div>
                    <button @click="openNotesModal(client.id)" class="px-3 py-1 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                        <i class="fas fa-comments ml-1"></i>
                        ملاحظات
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- Sidebar Widgets -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mt-4 md:mt-6" x-data="chartsData()" x-init="initCharts()">
    <!-- New Clients Chart -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">عدد العملاء الجدد شهريًا</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="newClientsChart"></canvas>
        </div>
    </div>

    <!-- Clients by Type Chart -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">توزيع العملاء حسب النوع</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="clientsByTypeChart"></canvas>
        </div>
    </div>

    <!-- Most Active Clients Chart -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">العملاء الأكثر نشاطًا</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="mostActiveClientsChart"></canvas>
        </div>
    </div>
</div>

<!-- Modals -->
@include('components.modals.client-attachment')
@include('components.modals.client-notes')

@push('scripts')
<script>
function clientFilters() {
    return {
        search: '',
        city: '',
        status: '',
        type: '',
        applyFilters() {
            console.log('Applying filters:', this);
        },
        clearFilters() {
            this.search = '';
            this.city = '';
            this.status = '';
            this.type = '';
        }
    }
}

function clientsData() {
    return {
        clients: [
            {
                id: 1,
                name: 'أحمد محمد العلي',
                type: 'individual',
                phone: '0501234567',
                email: 'ahmed@example.com',
                city: 'الرياض',
                district: 'العليا',
                projectsCount: 5,
                status: 'active',
                createdAt: '2025-01-15'
            },
            {
                id: 2,
                name: 'شركة البناء المتقدم',
                type: 'company',
                phone: '0112345678',
                email: 'info@company.com',
                city: 'جدة',
                district: 'الكورنيش',
                projectsCount: 12,
                status: 'active',
                createdAt: '2024-11-20'
            },
            {
                id: 3,
                name: 'وزارة الشؤون البلدية',
                type: 'government',
                phone: '0119876543',
                email: 'contact@municipality.gov.sa',
                city: 'الرياض',
                district: 'الملك فهد',
                projectsCount: 8,
                status: 'active',
                createdAt: '2024-09-10'
            },
            {
                id: 4,
                name: 'فاطمة سالم',
                type: 'individual',
                phone: '0509876543',
                email: 'fatima@example.com',
                city: 'الدمام',
                district: 'الكورنيش',
                projectsCount: 2,
                status: 'inactive',
                createdAt: '2025-03-05'
            }
        ],
        getClientTypeText(type) {
            const typeMap = {
                'individual': 'فرد',
                'company': 'شركة',
                'government': 'جهة حكومية'
            };
            return typeMap[type] || type;
        },
        openAttachmentModal(id) {
            window.dispatchEvent(new CustomEvent('open-client-attachment-modal', { detail: { clientId: id } }));
        },
        openNotesModal(id) {
            window.dispatchEvent(new CustomEvent('open-client-notes-modal', { detail: { clientId: id } }));
        },
        linkProject(id) {
            alert('ربط عميل #' + id + ' بمشروع جديد');
        },
        deleteClient(id) {
            if (confirm('هل أنت متأكد من حذف هذا العميل؟')) {
                console.log('Deleting client:', id);
            }
        }
    }
}

function chartsData() {
    return {
        newClientsChart: null,
        clientsByTypeChart: null,
        mostActiveChart: null,
        initCharts() {
            if (typeof Chart === 'undefined') {
                setTimeout(() => this.initCharts(), 100);
                return;
            }

            // New Clients Line Chart
            const newCtx = document.getElementById('newClientsChart');
            if (newCtx && !this.newClientsChart) {
                this.newClientsChart = new Chart(newCtx, {
                    type: 'line',
                    data: {
                        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر'],
                        datasets: [{
                            label: 'عملاء جدد',
                            data: [15, 18, 22, 20, 25, 28, 30, 27, 24, 26, 22],
                            borderColor: '#1db8f8',
                            backgroundColor: 'rgba(29, 184, 248, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: '#9ca3af'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#9ca3af'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)',
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Clients by Type Pie Chart
            const typeCtx = document.getElementById('clientsByTypeChart');
            if (typeCtx && !this.clientsByTypeChart) {
                this.clientsByTypeChart = new Chart(typeCtx, {
                    type: 'pie',
                    data: {
                        labels: ['فرد', 'شركة', 'جهة حكومية'],
                        datasets: [{
                            data: [120, 85, 40],
                            backgroundColor: ['#1db8f8', '#10b981', '#f59e0b']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#9ca3af',
                                    padding: 15
                                }
                            }
                        }
                    }
                });
            }

            // Most Active Clients Bar Chart
            const activeCtx = document.getElementById('mostActiveClientsChart');
            if (activeCtx && !this.mostActiveChart) {
                this.mostActiveChart = new Chart(activeCtx, {
                    type: 'bar',
                    data: {
                        labels: ['أحمد محمد', 'شركة البناء', 'وزارة الشؤون', 'فاطمة سالم', 'خالد مطر'],
                        datasets: [{
                            label: 'عدد المشاريع',
                            data: [12, 10, 8, 5, 4],
                            backgroundColor: '#1db8f8'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: '#9ca3af'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#9ca3af'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)',
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        }
    }
}
</script>
@endpush

@endsection


