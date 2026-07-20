<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Self-contained prayer-time calculator (astronomical algorithm, PrayTimes-style).
 *
 * No external service or package required — ideal for shared hosting. Supports
 * the major calculation methods and both Asr madhhabs (Standard / Hanafi).
 */
class PrayerTimes
{
    /** Calculation methods: fajr & isha angles (or isha "minutes after maghrib"). */
    public const METHODS = [
        'MWL' => ['name' => 'رابطة العالم الإسلامي', 'fajr' => 18.0, 'isha' => 17.0],
        'ISNA' => ['name' => 'أمريكا الشمالية (ISNA)', 'fajr' => 15.0, 'isha' => 15.0],
        'Egypt' => ['name' => 'الهيئة المصرية', 'fajr' => 19.5, 'isha' => 17.5],
        'Makkah' => ['name' => 'أم القرى (مكة)', 'fajr' => 18.5, 'isha' => 90.0, 'ishaIsMinutes' => true],
        'Karachi' => ['name' => 'كراتشي', 'fajr' => 18.0, 'isha' => 18.0],
        'Dubai' => ['name' => 'دبي', 'fajr' => 18.2, 'isha' => 18.2],
        'Kuwait' => ['name' => 'الكويت', 'fajr' => 18.0, 'isha' => 17.5],
        'Qatar' => ['name' => 'قطر', 'fajr' => 18.0, 'isha' => 90.0, 'ishaIsMinutes' => true],
        'Turkey' => ['name' => 'تركيا (Diyanet)', 'fajr' => 18.0, 'isha' => 17.0],
        'Tehran' => ['name' => 'طهران', 'fajr' => 17.7, 'isha' => 14.0, 'maghrib' => 4.5],
    ];

    protected const RISE_SET_ANGLE = 0.833;

    /**
     * Compute prayer times for a date/location.
     *
     * @return array<string,string> keyed fajr,sunrise,dhuhr,asr,maghrib,isha as "HH:MM"
     */
    public function times(
        Carbon $date,
        float $lat,
        float $lng,
        float $timezone,
        string $method = 'MWL',
        string $asr = 'Standard',
    ): array {
        $params = self::METHODS[$method] ?? self::METHODS['MWL'];
        $asrFactor = strtolower($asr) === 'hanafi' ? 2 : 1;

        $jDate = $this->julian((int) $date->year, (int) $date->month, (int) $date->day)
            - $lng / (15 * 24);

        // Initial guesses (day portions).
        $portions = [
            'fajr' => 5 / 24, 'sunrise' => 6 / 24, 'dhuhr' => 12 / 24,
            'asr' => 13 / 24, 'sunset' => 18 / 24, 'isha' => 18 / 24,
        ];

        // Refine over a couple of iterations.
        for ($i = 0; $i < 3; $i++) {
            $fajr = $this->sunAngleTime($params['fajr'], $portions['fajr'], $jDate, $lat, true);
            $sunrise = $this->sunAngleTime(self::RISE_SET_ANGLE, $portions['sunrise'], $jDate, $lat, true);
            $dhuhr = $this->midDay($portions['dhuhr'], $jDate);
            $asrT = $this->asrTime($asrFactor, $portions['asr'], $jDate, $lat);
            $sunset = $this->sunAngleTime(self::RISE_SET_ANGLE, $portions['sunset'], $jDate, $lat);

            $portions = [
                'fajr' => $fajr / 24, 'sunrise' => $sunrise / 24, 'dhuhr' => $dhuhr / 24,
                'asr' => $asrT / 24, 'sunset' => $sunset / 24, 'isha' => $sunset / 24,
            ];
        }

        $maghrib = $sunset;
        if (isset($params['maghrib'])) {
            $maghrib = $this->sunAngleTime($params['maghrib'], $portions['sunset'], $jDate, $lat);
        }

        if (! empty($params['ishaIsMinutes'])) {
            $isha = $maghrib + $params['isha'] / 60;
        } else {
            $isha = $this->sunAngleTime($params['isha'], $portions['isha'], $jDate, $lat);
        }

        $adjust = fn ($t) => $t + $timezone - $lng / 15;

        return [
            'fajr' => $this->fmt($adjust($fajr)),
            'sunrise' => $this->fmt($adjust($sunrise)),
            'dhuhr' => $this->fmt($adjust($dhuhr)),
            'asr' => $this->fmt($adjust($asrT)),
            'maghrib' => $this->fmt($adjust($maghrib)),
            'isha' => $this->fmt($adjust($isha)),
        ];
    }

    // ---- Astronomical core --------------------------------------------------

    protected function julian(int $year, int $month, int $day): float
    {
        if ($month <= 2) {
            $year -= 1;
            $month += 12;
        }
        $a = floor($year / 100);
        $b = 2 - $a + floor($a / 4);

        return floor(365.25 * ($year + 4716)) + floor(30.6001 * ($month + 1)) + $day + $b - 1524.5;
    }

    /** @return array{declination:float,equation:float} */
    protected function sunPosition(float $jd): array
    {
        $d = $jd - 2451545.0;
        $g = $this->fixAngle(357.529 + 0.98560028 * $d);
        $q = $this->fixAngle(280.459 + 0.98564736 * $d);
        $l = $this->fixAngle($q + 1.915 * $this->dsin($g) + 0.020 * $this->dsin(2 * $g));
        $e = 23.439 - 0.00000036 * $d;

        $decl = $this->darcsin($this->dsin($e) * $this->dsin($l));
        $ra = $this->fixHour($this->darctan2($this->dcos($e) * $this->dsin($l), $this->dcos($l)) / 15);
        $eqt = $q / 15 - $ra;

        return ['declination' => $decl, 'equation' => $eqt];
    }

    protected function midDay(float $t, float $jd): float
    {
        $eqt = $this->sunPosition($jd + $t)['equation'];

        return $this->fixHour(12 - $eqt);
    }

    protected function sunAngleTime(float $angle, float $t, float $jd, float $lat, bool $ccw = false): float
    {
        $decl = $this->sunPosition($jd + $t)['declination'];
        $noon = $this->midDay($t, $jd);
        $val = (-$this->dsin($angle) - $this->dsin($decl) * $this->dsin($lat))
            / ($this->dcos($decl) * $this->dcos($lat));
        $val = max(-1, min(1, $val));
        $offset = (1 / 15) * $this->darccos($val);

        return $noon + ($ccw ? -$offset : $offset);
    }

    protected function asrTime(int $factor, float $t, float $jd, float $lat): float
    {
        $decl = $this->sunPosition($jd + $t)['declination'];
        $angle = -$this->darccot($factor + $this->dtan(abs($lat - $decl)));

        return $this->sunAngleTime($angle, $t, $jd, $lat);
    }

    // ---- Trig helpers (degrees) --------------------------------------------

    protected function dsin(float $d): float { return sin(deg2rad($d)); }
    protected function dcos(float $d): float { return cos(deg2rad($d)); }
    protected function dtan(float $d): float { return tan(deg2rad($d)); }
    protected function darcsin(float $x): float { return rad2deg(asin($x)); }
    protected function darccos(float $x): float { return rad2deg(acos($x)); }
    protected function darctan2(float $y, float $x): float { return rad2deg(atan2($y, $x)); }
    protected function darccot(float $x): float { return rad2deg(atan2(1, $x)); }

    protected function fixAngle(float $a): float
    {
        $a = $a - 360 * floor($a / 360);

        return $a < 0 ? $a + 360 : $a;
    }

    protected function fixHour(float $h): float
    {
        $h = $h - 24 * floor($h / 24);

        return $h < 0 ? $h + 24 : $h;
    }

    protected function fmt(float $time): string
    {
        $time = $this->fixHour($time + 0.5 / 60); // round to nearest minute
        $hours = (int) floor($time);
        $minutes = (int) floor(($time - $hours) * 60);

        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
