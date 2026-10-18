<?php

namespace App\Support;

/**
 * ترجمة القيم المخزّنة في قاعدة البيانات إلى نصوص عربية للعرض.
 */
class Labels
{
    public static function category(?string $value): string
    {
        return [
            'tuition' => 'رسوم دراسية',
            'registration' => 'رسوم تسجيل',
            'laboratory' => 'رسوم معامل',
            'other' => 'رسوم أخرى إلزامية',
        ][$value] ?? (string) $value;
    }

    public static function frequency(?string $value): string
    {
        return [
            'one_time' => 'مرة واحدة',
            'per_semester' => 'كل فصل',
            'per_year' => 'كل سنة',
        ][$value] ?? (string) $value;
    }

    public static function degree(?string $value): string
    {
        return [
            'diploma' => 'دبلوم',
            'bachelor' => 'بكالوريوس',
            'master' => 'ماجستير',
        ][$value] ?? (string) $value;
    }

    public static function courseType(?string $value): string
    {
        return [
            'theoretical' => 'نظري',
            'practical' => 'عملي',
            'mixed' => 'نظري وعملي',
        ][$value] ?? (string) $value;
    }

    public static function facilityType(?string $value): string
    {
        return [
            'facility' => 'مرفق',
            'service' => 'خدمة',
        ][$value] ?? (string) $value;
    }

    public static function dataStatus(?string $value): string
    {
        return [
            'verified' => 'موثّق',
            'synthetic' => 'بيانات تجريبية',
            'unavailable' => 'غير متوفر',
        ][$value] ?? (string) $value;
    }

    public static function dataStatusClass(?string $value): string
    {
        return [
            'verified' => 'success',
            'synthetic' => 'warning',
            'unavailable' => 'secondary',
        ][$value] ?? 'secondary';
    }

    /** يعرض القيمة، أو "غير متوفر" إذا كانت null (ولا يعامل الصفر كغير متوفر) */
    public static function orNA($value, string $suffix = ''): string
    {
        return $value === null ? 'غير متوفر' : $value . $suffix;
    }
}
