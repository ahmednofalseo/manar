<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 hover:border-primary-400/40 transition-all duration-200 border border-white/10">
    <!-- Header -->
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <h3 class="text-lg md:text-xl font-bold text-white mb-1" x-text="project.name"></h3>
            <p class="text-gray-400 text-xs md:text-sm" x-text="project.projectNumber"></p>
        </div>
        <span 
            class="px-3 py-1 rounded-lg text-xs font-semibold whitespace-nowrap"
            :class="{
                'bg-primary-500/20 text-primary-400': project.typeBadge === 'primary',
                'bg-green-500/20 text-green-400': project.typeBadge === 'green',
                'bg-purple-500/20 text-purple-400': project.typeBadge === 'purple',
                'bg-blue-500/20 text-blue-400': project.typeBadge === 'blue'
            }"
            x-text="project.type"
        ></span>
    </div>

    <!-- Location & Owner -->
    <div class="space-y-2 mb-4">
        <div class="flex items-center gap-2 text-gray-300 text-sm">
            <i class="fas fa-map-marker-alt text-primary-400"></i>
            <span x-text="project.city + ' / ' + project.district"></span>
        </div>
        <div class="flex items-center gap-2 text-gray-300 text-sm">
            <i class="fas fa-user text-primary-400"></i>
            <span x-text="project.owner"></span>
        </div>
        <div class="flex items-center gap-2 text-gray-300 text-sm">
            <i class="fas fa-money-bill-wave text-primary-400"></i>
            <span x-text="new Intl.NumberFormat('ar-SA').format(project.value) + ' ر.س'"></span>
        </div>
    </div>

    <!-- Progress Circle -->
    <div class="flex items-center gap-4 mb-4">
        <div class="relative w-16 h-16 md:w-20 md:h-20">
            <svg class="transform -rotate-90 w-full h-full" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="16" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2"/>
                <circle 
                    cx="18" 
                    cy="18" 
                    r="16" 
                    fill="none" 
                    stroke="#1db8f8" 
                    stroke-width="2"
                    stroke-dasharray="100"
                    :stroke-dashoffset="100 - project.progress"
                    stroke-linecap="round"
                />
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-white font-bold text-sm md:text-base" x-text="project.progress + '%'"></span>
            </div>
        </div>
        <div class="flex-1">
            <p class="text-gray-400 text-xs mb-1">التقدّم</p>
            <span 
                class="px-3 py-1 rounded-lg text-xs font-semibold"
                :class="{
                    'bg-blue-500/20 text-blue-400': project.stageBadge === 'blue',
                    'bg-yellow-500/20 text-yellow-400': project.stageBadge === 'yellow',
                    'bg-green-500/20 text-green-400': project.stageBadge === 'green',
                    'bg-purple-500/20 text-purple-400': project.stageBadge === 'purple'
                }"
                x-text="project.currentStage"
            ></span>
        </div>
    </div>

    <!-- Quick Icons -->
    <div class="flex items-center gap-4 mb-4 pt-4 border-t border-white/10">
        <div class="flex items-center gap-2 text-gray-400 text-xs">
            <i class="fas fa-paperclip"></i>
            <span x-text="project.attachments"></span>
        </div>
        <div class="flex items-center gap-2 text-gray-400 text-xs">
            <i class="fas fa-list-check"></i>
            <span x-text="project.tasks"></span>
        </div>
        <div class="flex items-center gap-2 text-gray-400 text-xs">
            <i class="fas fa-file-invoice-dollar"></i>
            <span x-text="project.invoices"></span>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-2">
        <a 
            :href="`{{ url('/projects') }}/${project.id}`"
            class="flex-1 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 px-3 py-2 rounded-lg text-center text-xs md:text-sm transition-all duration-200"
        >
            <i class="fas fa-eye ml-1"></i>
            عرض
        </a>
        <a 
            :href="`{{ url('/projects') }}/${project.id}/edit`"
            class="flex-1 bg-white/5 hover:bg-white/10 text-white px-3 py-2 rounded-lg text-center text-xs md:text-sm transition-all duration-200"
        >
            <i class="fas fa-edit ml-1"></i>
            تحرير
        </a>
    </div>
</div>
