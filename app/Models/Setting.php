<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    /**
     * الحصول على قيمة إعداد
     */
    public static function get($key, $default = null)
    {
        // استخدام الكاش لتحسين الأداء
        $cacheKey = "setting_{$key}";
        return \Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * حفظ قيمة إعداد
     */
    public static function set($key, $value)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        
        // مسح الكاش بعد التحديث
        \Cache::forget("setting_{$key}");
        
        return $setting;
    }

    /**
     * الحصول على جميع الإعدادات حسب المجموعة
     */
    public static function getByGroup($group)
    {
        return self::where('group', $group)->get()->pluck('value', 'key')->toArray();
    }

    /**
     * حفظ عدة إعدادات دفعة واحدة
     */
    public static function setMany(array $settings)
    {
        foreach ($settings as $key => $value) {
            self::set($key, $value);
        }
    }
}
