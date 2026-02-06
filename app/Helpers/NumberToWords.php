<?php

namespace App\Helpers;

class NumberToWords
{
    private static $ones = [
        0 => '',
        1 => 'واحد',
        2 => 'اثنان',
        3 => 'ثلاثة',
        4 => 'أربعة',
        5 => 'خمسة',
        6 => 'ستة',
        7 => 'سبعة',
        8 => 'ثمانية',
        9 => 'تسعة',
        10 => 'عشرة',
        11 => 'أحد عشر',
        12 => 'اثنا عشر',
        13 => 'ثلاثة عشر',
        14 => 'أربعة عشر',
        15 => 'خمسة عشر',
        16 => 'ستة عشر',
        17 => 'سبعة عشر',
        18 => 'ثمانية عشر',
        19 => 'تسعة عشر',
    ];

    private static $tens = [
        2 => 'عشرون',
        3 => 'ثلاثون',
        4 => 'أربعون',
        5 => 'خمسون',
        6 => 'ستون',
        7 => 'سبعون',
        8 => 'ثمانون',
        9 => 'تسعون',
    ];

    private static $hundreds = [
        1 => 'مائة',
        2 => 'مائتان',
        3 => 'ثلاثمائة',
        4 => 'أربعمائة',
        5 => 'خمسمائة',
        6 => 'ستمائة',
        7 => 'سبعمائة',
        8 => 'ثمانمائة',
        9 => 'تسعمائة',
    ];

    private static $thousands = [
        1 => 'ألف',
        2 => 'ألفان',
        3 => 'ثلاثة آلاف',
        4 => 'أربعة آلاف',
        5 => 'خمسة آلاف',
        6 => 'ستة آلاف',
        7 => 'سبعة آلاف',
        8 => 'ثمانية آلاف',
        9 => 'تسعة آلاف',
    ];

    private static $millions = [
        1 => 'مليون',
        2 => 'مليونان',
        3 => 'ثلاثة ملايين',
        4 => 'أربعة ملايين',
        5 => 'خمسة ملايين',
        6 => 'ستة ملايين',
        7 => 'سبعة ملايين',
        8 => 'ثمانية ملايين',
        9 => 'تسعة ملايين',
    ];

    public static function convert($number)
    {
        if ($number == 0) {
            return 'صفر';
        }

        $number = (float) $number;
        $integerPart = (int) $number;
        $decimalPart = round(($number - $integerPart) * 100);

        $result = self::convertInteger($integerPart);

        if ($decimalPart > 0) {
            $result .= ' ريال و ' . self::convertInteger($decimalPart) . ' هللة';
        } else {
            $result .= ' ريال';
        }

        return $result . ' فقط لا غير';
    }

    private static function convertInteger($number)
    {
        if ($number == 0) {
            return '';
        }

        if ($number < 20) {
            return self::$ones[$number];
        }

        if ($number < 100) {
            $tens = (int)($number / 10);
            $ones = $number % 10;

            if ($ones == 0) {
                return self::$tens[$tens];
            }

            return self::$ones[$ones] . ' و' . self::$tens[$tens];
        }

        if ($number < 1000) {
            $hundreds = (int)($number / 100);
            $remainder = $number % 100;

            if ($remainder == 0) {
                return self::$hundreds[$hundreds];
            }

            return self::$hundreds[$hundreds] . ' و' . self::convertInteger($remainder);
        }

        if ($number < 1000000) {
            $thousands = (int)($number / 1000);
            $remainder = $number % 1000;

            $thousandsText = '';
            if ($thousands == 1) {
                $thousandsText = 'ألف';
            } elseif ($thousands == 2) {
                $thousandsText = 'ألفان';
            } elseif ($thousands < 11) {
                $thousandsText = self::convertInteger($thousands) . ' آلاف';
            } else {
                $thousandsText = self::convertInteger($thousands) . ' ألف';
            }

            if ($remainder == 0) {
                return $thousandsText;
            }

            return $thousandsText . ' و' . self::convertInteger($remainder);
        }

        if ($number < 1000000000) {
            $millions = (int)($number / 1000000);
            $remainder = $number % 1000000;

            $millionsText = '';
            if ($millions == 1) {
                $millionsText = 'مليون';
            } elseif ($millions == 2) {
                $millionsText = 'مليونان';
            } elseif ($millions < 11) {
                $millionsText = self::convertInteger($millions) . ' ملايين';
            } else {
                $millionsText = self::convertInteger($millions) . ' مليون';
            }

            if ($remainder == 0) {
                return $millionsText;
            }

            return $millionsText . ' و' . self::convertInteger($remainder);
        }

        return 'رقم كبير جداً';
    }
}
