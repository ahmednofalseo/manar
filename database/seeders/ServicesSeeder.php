<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\WorkflowTemplate;
use App\Models\WorkflowStep;
use Illuminate\Support\Str;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء فئات الخدمات
        $categories = [
            ['name' => 'خدمات التصميم', 'slug' => 'design-services', 'order' => 1],
            ['name' => 'خدمات الإشراف', 'slug' => 'supervision-services', 'order' => 2],
            ['name' => 'خدمات بلدي', 'slug' => 'municipal-services', 'order' => 3],
            ['name' => 'خدمات أخرى', 'slug' => 'other-services', 'order' => 4],
        ];

        foreach ($categories as $category) {
            ServiceCategory::create($category);
        }

        $designCategory = ServiceCategory::where('slug', 'design-services')->first();
        $supervisionCategory = ServiceCategory::where('slug', 'supervision-services')->first();
        $municipalCategory = ServiceCategory::where('slug', 'municipal-services')->first();
        $otherCategory = ServiceCategory::where('slug', 'other-services')->first();

        // إنشاء الخدمات الأساسية
        $services = [
            [
                'name' => 'تصميم',
                'slug' => 'design',
                'category_id' => $designCategory->id,
                'order' => 1,
            ],
            [
                'name' => 'تصميم وإشراف',
                'slug' => 'design-and-supervision',
                'category_id' => $designCategory->id,
                'order' => 2,
            ],
            [
                'name' => 'إشراف',
                'slug' => 'supervision',
                'category_id' => $supervisionCategory->id,
                'order' => 1,
            ],
            [
                'name' => 'مخطط الدفاع المدني',
                'slug' => 'civil-defense-plan',
                'category_id' => $otherCategory->id,
                'order' => 1,
            ],
            [
                'name' => 'رفع مساحي',
                'slug' => 'survey',
                'category_id' => $otherCategory->id,
                'order' => 2,
            ],
            [
                'name' => 'تحديث صك',
                'slug' => 'deed-update',
                'category_id' => $otherCategory->id,
                'order' => 3,
            ],
            [
                'name' => 'تصميم داخلي',
                'slug' => 'interior-design',
                'category_id' => $designCategory->id,
                'order' => 3,
            ],
            [
                'name' => 'تصميم خارجي وداخلي',
                'slug' => 'exterior-interior-design',
                'category_id' => $designCategory->id,
                'order' => 4,
            ],
            [
                'name' => 'خدمات بلدي',
                'slug' => 'municipal-services',
                'category_id' => $municipalCategory->id,
                'has_sub_services' => true,
                'order' => 1,
            ],
            [
                'name' => 'تقارير فنية',
                'slug' => 'technical-reports',
                'category_id' => $otherCategory->id,
                'order' => 4,
            ],
            [
                'name' => 'إدارة مشاريع',
                'slug' => 'project-management',
                'category_id' => $otherCategory->id,
                'order' => 5,
            ],
        ];

        $createdServices = [];
        foreach ($services as $service) {
            $createdServices[$service['slug']] = Service::create($service);
        }

        // إنشاء خدمات بلدي الفرعية
        $municipalService = $createdServices['municipal-services'];
        $municipalSubServices = [
            ['name' => 'رخصة بناء', 'slug' => 'building-permit', 'order' => 1],
            ['name' => 'رخصة تشغيل', 'slug' => 'operating-permit', 'order' => 2],
            ['name' => 'رخصة تعديل', 'slug' => 'modification-permit', 'order' => 3],
            ['name' => 'إخلاء طرف', 'slug' => 'clearance', 'order' => 4],
        ];

        foreach ($municipalSubServices as $subService) {
            Service::create([
                'name' => $subService['name'],
                'slug' => $subService['slug'],
                'parent_id' => $municipalService->id,
                'category_id' => $municipalCategory->id,
                'order' => $subService['order'],
            ]);
        }

        // إنشاء قالب مسار افتراضي لخدمة "تصميم"
        $designService = $createdServices['design'];
        $this->createDefaultWorkflowTemplate($designService, 'تصميم', [
            ['name' => 'المراجعة الأولية', 'department' => 'معماري', 'duration' => 3, 'order' => 1],
            ['name' => 'التصميم المعماري', 'department' => 'معماري', 'duration' => 14, 'order' => 2],
            ['name' => 'التصميم الإنشائي', 'department' => 'إنشائي', 'duration' => 7, 'order' => 3],
            ['name' => 'التصميم الكهربائي', 'department' => 'كهربائي', 'duration' => 5, 'order' => 4],
            ['name' => 'التصميم الميكانيكي', 'department' => 'ميكانيكي', 'duration' => 5, 'order' => 5],
            ['name' => 'المراجعة النهائية', 'department' => 'معماري', 'duration' => 3, 'order' => 6],
        ]);

        // إنشاء قالب مسار افتراضي لخدمة "تصميم وإشراف"
        $designSupervisionService = $createdServices['design-and-supervision'];
        $this->createDefaultWorkflowTemplate($designSupervisionService, 'تصميم وإشراف', [
            ['name' => 'المراجعة الأولية', 'department' => 'معماري', 'duration' => 3, 'order' => 1],
            ['name' => 'التصميم المعماري', 'department' => 'معماري', 'duration' => 14, 'order' => 2],
            ['name' => 'التصميم الإنشائي', 'department' => 'إنشائي', 'duration' => 7, 'order' => 3],
            ['name' => 'التصميم الكهربائي', 'department' => 'كهربائي', 'duration' => 5, 'order' => 4],
            ['name' => 'التصميم الميكانيكي', 'department' => 'ميكانيكي', 'duration' => 5, 'order' => 5],
            ['name' => 'المراجعة النهائية', 'department' => 'معماري', 'duration' => 3, 'order' => 6],
            ['name' => 'الإشراف على التنفيذ', 'department' => 'معماري', 'duration' => 30, 'order' => 7],
        ]);
    }

    /**
     * إنشاء قالب مسار افتراضي
     */
    private function createDefaultWorkflowTemplate(Service $service, string $name, array $steps): void
    {
        $template = WorkflowTemplate::create([
            'service_id' => $service->id,
            'name' => "مسار {$name} الافتراضي",
            'description' => "مسار افتراضي لخدمة {$service->name}",
            'is_default' => true,
            'is_active' => true,
        ]);

        foreach ($steps as $step) {
            WorkflowStep::create([
                'workflow_template_id' => $template->id,
                'name' => $step['name'],
                'description' => null,
                'order' => $step['order'],
                'department' => $step['department'],
                'default_duration_days' => $step['duration'],
                'expected_outputs' => ['files', 'approvals'],
                'dependencies' => $step['order'] > 1 ? [$step['order'] - 1] : null,
                'is_parallel' => false,
                'is_required' => true,
            ]);
        }
    }
}
