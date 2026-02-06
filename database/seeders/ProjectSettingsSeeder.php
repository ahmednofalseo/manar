<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProjectType;
use App\Models\City;
use App\Models\StageSetting;

class ProjectSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // أنواع المشاريع
        $projectTypes = [
            ['name' => 'تصميم', 'name_en' => 'Design', 'order' => 1, 'is_active' => true],
            ['name' => 'تصميم وإشراف', 'name_en' => 'Design & Supervision', 'order' => 2, 'is_active' => true],
            ['name' => 'إشراف', 'name_en' => 'Supervision', 'order' => 3, 'is_active' => true],
            ['name' => 'تقرير فني', 'name_en' => 'Technical Report', 'order' => 4, 'is_active' => true],
            ['name' => 'تقرير دفاع مدني', 'name_en' => 'Civil Defense Report', 'order' => 5, 'is_active' => true],
            ['name' => 'تصميم دفاع مدني', 'name_en' => 'Civil Defense Design', 'order' => 6, 'is_active' => true],
            ['name' => 'تعديلات', 'name_en' => 'Modifications', 'order' => 7, 'is_active' => true],
            ['name' => 'استشارات', 'name_en' => 'Consultations', 'order' => 8, 'is_active' => true],
        ];

        foreach ($projectTypes as $type) {
            ProjectType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }

        // المدن
        $cities = [
            ['name' => 'الرياض', 'name_en' => 'Riyadh', 'code' => 'RUH', 'order' => 1, 'is_active' => true],
            ['name' => 'جدة', 'name_en' => 'Jeddah', 'code' => 'JED', 'order' => 2, 'is_active' => true],
            ['name' => 'الدمام', 'name_en' => 'Dammam', 'code' => 'DMM', 'order' => 3, 'is_active' => true],
            ['name' => 'مكة', 'name_en' => 'Makkah', 'code' => 'MAK', 'order' => 4, 'is_active' => true],
            ['name' => 'المدينة', 'name_en' => 'Madinah', 'code' => 'MED', 'order' => 5, 'is_active' => true],
            ['name' => 'الطائف', 'name_en' => 'Taif', 'code' => 'TIF', 'order' => 6, 'is_active' => true],
            ['name' => 'أبها', 'name_en' => 'Abha', 'code' => 'AHB', 'order' => 7, 'is_active' => true],
            ['name' => 'بريدة', 'name_en' => 'Buraydah', 'code' => 'BRD', 'order' => 8, 'is_active' => true],
        ];

        foreach ($cities as $city) {
            City::firstOrCreate(
                ['name' => $city['name']],
                $city
            );
        }

        // المراحل
        $stages = [
            ['name' => 'معماري', 'name_en' => 'Architectural', 'icon' => 'fas fa-building', 'color' => '#1db8f8', 'order' => 1, 'is_active' => true],
            ['name' => 'إنشائي', 'name_en' => 'Structural', 'icon' => 'fas fa-hard-hat', 'color' => '#f59e0b', 'order' => 2, 'is_active' => true],
            ['name' => 'كهربائي', 'name_en' => 'Electrical', 'icon' => 'fas fa-bolt', 'color' => '#fbbf24', 'order' => 3, 'is_active' => true],
            ['name' => 'ميكانيكي', 'name_en' => 'Mechanical', 'icon' => 'fas fa-cog', 'color' => '#8b5cf6', 'order' => 4, 'is_active' => true],
            ['name' => 'صحي/بيئي', 'name_en' => 'Sanitary/Environmental', 'icon' => 'fas fa-tint', 'color' => '#06b6d4', 'order' => 5, 'is_active' => true],
            ['name' => 'تقديم للبلدية', 'name_en' => 'Municipality Submission', 'icon' => 'fas fa-file-alt', 'color' => '#10b981', 'order' => 6, 'is_active' => true],
            ['name' => 'أخرى', 'name_en' => 'Other', 'icon' => 'fas fa-ellipsis-h', 'color' => '#6b7280', 'order' => 7, 'is_active' => true],
        ];

        foreach ($stages as $stage) {
            StageSetting::firstOrCreate(
                ['name' => $stage['name']],
                $stage
            );
        }
    }
}
