<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingsHelper
{
    /**
     * الحصول على قيمة إعداد
     */
    public static function get($key, $default = null)
    {
        return Setting::get($key, $default);
    }

    /**
     * الحصول على اسم النظام
     */
    public static function systemName()
    {
        return self::get('system_name', 'المنار');
    }

    /**
     * الحصول على لوجو النظام
     */
    public static function systemLogo()
    {
        $logo = self::get('system_logo');
        return $logo ? asset('storage/' . $logo) : null;
    }

    /**
     * الحصول على اللغة
     */
    public static function language()
    {
        return self::get('language', 'ar');
    }

    /**
     * الحصول على المنطقة الزمنية
     */
    public static function timezone()
    {
        return self::get('timezone', 'Asia/Riyadh');
    }

    /**
     * الحصول على تنسيق التاريخ
     */
    public static function dateFormat()
    {
        return self::get('date_format', 'Y-m-d');
    }

    /**
     * الحصول على تنسيق الوقت
     */
    public static function timeFormat()
    {
        return self::get('time_format', 'H:i');
    }

    /**
     * تنسيق التاريخ والوقت
     */
    public static function formatDateTime($dateTime)
    {
        if (!$dateTime) {
            return null;
        }

        $timezone = self::timezone();
        $dateFormat = self::dateFormat();
        $timeFormat = self::timeFormat();

        if ($dateTime instanceof \Carbon\Carbon) {
            return $dateTime->setTimezone($timezone)->format($dateFormat . ' ' . $timeFormat);
        }

        return \Carbon\Carbon::parse($dateTime)->setTimezone($timezone)->format($dateFormat . ' ' . $timeFormat);
    }

    /**
     * الحصول على إعدادات البريد الإلكتروني
     */
    public static function mailSettings()
    {
        return Setting::getByGroup('email');
    }

    /**
     * الحصول على بيانات الدعم الفني
     */
    public static function supportSettings()
    {
        return Setting::getByGroup('support');
    }
}



