<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['first_step', 'أول خطوة', 'First Step', 'سجّلت أوّل عبادة لك', 'bronze'],
            ['week_warrior', 'أسبوع كامل', 'Full Week', '٧ أيام متتالية من العبادة', 'silver'],
            ['forty_days', 'أربعون يوماً', 'Forty Days', '٤٠ يوماً متتالياً — عادة راسخة', 'gold'],
            ['hundred_days', 'مئة يوم', 'Hundred Days', '١٠٠ يوم متتالٍ بإذن الله', 'gold'],
            ['salat_keeper', 'محافظ على الصلاة', 'Salah Keeper', '٧ أيام صلاة متتالية', 'silver'],
            ['quran_companion', 'رفيق القرآن', 'Quran Companion', '٧ أيام قراءة متتالية', 'silver'],
            ['first_khatma', 'أوّل ختمة', 'First Khatma', 'أتممت ختمة كاملة للقرآن', 'gold'],
            ['azkar_devotee', 'مداوم على الأذكار', 'Azkar Devotee', '٧ أيام أذكار متتالية', 'silver'],
            ['generous_soul', 'يدٌ كريمة', 'Generous Soul', 'سجّلت ١٠ صدقات', 'silver'],
            ['rising_star', 'نجمٌ صاعد', 'Rising Star', 'بلغت المستوى الخامس', 'gold'],
        ];

        foreach ($badges as $i => [$key, $ar, $en, $desc, $tier]) {
            Badge::updateOrCreate(
                ['key' => $key],
                ['name_ar' => $ar, 'name_en' => $en, 'description_ar' => $desc, 'tier' => $tier, 'sort' => $i],
            );
        }
    }
}
