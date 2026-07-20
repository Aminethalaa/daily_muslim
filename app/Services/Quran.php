<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Reads the bundled Uthmani Quran dataset (resources/data/quran). Self-contained
 * and offline — no external service. Source: risan/quran-json (Tanzil text).
 */
class Quran
{
    protected string $base;

    public function __construct()
    {
        $this->base = resource_path('data/quran');
    }

    /** @return array<int,array{id:int,name:string,transliteration:string,type:string,total_verses:int}> */
    public function surahs(): array
    {
        return Cache::rememberForever('quran.index', function () {
            return json_decode(file_get_contents($this->base.'/index.json'), true) ?: [];
        });
    }

    public function surahMeta(int $id): ?array
    {
        foreach ($this->surahs() as $s) {
            if ($s['id'] === $id) {
                return $s;
            }
        }

        return null;
    }

    /** @return array<int,array{id:int,text:string}> */
    public function verses(int $surahId): array
    {
        $path = $this->base."/surah_{$surahId}.json";
        if (! is_file($path)) {
            return [];
        }

        return Cache::rememberForever("quran.surah.$surahId", function () use ($path) {
            return json_decode(file_get_contents($path), true) ?: [];
        });
    }

    /** Rough page estimate for a surah (mushaf ≈ 15 lines/page, ~ verse density). */
    public function estimatePages(int $surahId): float
    {
        $meta = $this->surahMeta($surahId);
        if (! $meta) {
            return 1;
        }

        return max(0.5, round($meta['total_verses'] / 12, 1));
    }
}
