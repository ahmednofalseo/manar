<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Approval;
use App\Enums\ProjectStageKey;

class StageFlowService
{
    /**
     * التحقق من إمكانية الانتقال للمرحلة التالية
     */
    public function canAdvanceToNextStage(Project $project, string $currentStageKey): bool
    {
        // الحصول على جميع الموافقات المطلوبة للمرحلة الحالية
        $requiredApprovals = Approval::where('project_id', $project->id)
            ->where('stage_key', $currentStageKey)
            ->where('status', 'pending')
            ->count();

        // إذا كانت هناك موافقات قيد الانتظار، لا يمكن الانتقال
        if ($requiredApprovals > 0) {
            return false;
        }

        // التحقق من أن جميع الموافقات المطلوبة للمرحلة الحالية تمت الموافقة عليها
        $allApproved = Approval::where('project_id', $project->id)
            ->where('stage_key', $currentStageKey)
            ->where('status', '!=', 'approved')
            ->count() === 0;

        return $allApproved;
    }

    /**
     * التحقق من الموافقات والانتقال للمرحلة التالية تلقائياً
     */
    public function checkAndAdvanceStage(Project $project, string $stageKey): void
    {
        // التحقق من أن جميع الموافقات المطلوبة للمرحلة الحالية تمت الموافقة عليها
        if (!$this->canAdvanceToNextStage($project, $stageKey)) {
            return;
        }

        // الحصول على المرحلة التالية
        $nextStage = $this->getNextStage($project, $stageKey);
        
        if ($nextStage) {
            // تحديث المرحلة الحالية
            $project->update(['current_stage' => $nextStage]);
            
            // تحديث حالة المرحلة في project_stages
            $projectStage = $project->projectStages()
                ->where('stage_name', $this->getStageName($nextStage))
                ->first();
            
            if ($projectStage && $projectStage->status === 'جديد') {
                $projectStage->update([
                    'status' => 'جارٍ',
                    'start_date' => now(),
                ]);
            }
        }
    }

    /**
     * الحصول على المرحلة التالية
     */
    protected function getNextStage(Project $project, string $currentStageKey): ?string
    {
        if (!$project->stages || empty($project->stages)) {
            return null;
        }

        $stageOrder = [
            'architectural' => 'معماري',
            'structural' => 'إنشائي',
            'electrical' => 'كهربائي',
            'mechanical' => 'ميكانيكي',
            'municipality' => 'بلدي',
            'health_environmental' => 'صحي/بيئي',
            'civil_defense' => 'دفاع مدني',
        ];

        $currentStageName = $stageOrder[$currentStageKey] ?? null;
        if (!$currentStageName) {
            return null;
        }

        $stages = $project->stages;
        $currentIndex = array_search($currentStageName, $stages);

        if ($currentIndex === false || $currentIndex === count($stages) - 1) {
            return null;
        }

        return $stages[$currentIndex + 1];
    }

    /**
     * الحصول على مفتاح المرحلة من اسمها
     */
    protected function getStageName(string $stageKey): string
    {
        $stageNames = [
            'architectural' => 'معماري',
            'structural' => 'إنشائي',
            'electrical' => 'كهربائي',
            'mechanical' => 'ميكانيكي',
            'municipality' => 'بلدي',
            'health_environmental' => 'صحي/بيئي',
            'civil_defense' => 'دفاع مدني',
        ];

        return $stageNames[$stageKey] ?? $stageKey;
    }

    /**
     * التحقق من أن المرحلة جاهزة للانتقال (Policy check)
     */
    public function isStageReadyForAdvancement(Project $project, string $stageKey): bool
    {
        return $this->canAdvanceToNextStage($project, $stageKey);
    }
}




