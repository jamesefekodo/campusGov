<?php

namespace App\Services;

use App\Models\Tenancy\Tenant;
use App\Models\Theme\Frontend\FrontendTheme;
use Illuminate\Support\Facades\Cache;

class ThemeService
{
    public function forTenant(Tenant $tenant): array
    {
        return Cache::remember(
            "tenant_theme_{$tenant->id}",
            now()->addHours(24),
            function () use ($tenant) {
                $setting = $tenant->themeSetting()->with('theme')->first();

                if (! $setting || ! $setting->theme) {
                    $theme = FrontendTheme::where('slug', 'aurora')->firstOrFail();

                    return $theme->default_colors;
                }

                return array_merge(
                    $setting->theme->default_colors ?? [],
                    $setting->color_overrides ?? []
                );
            }
        );
    }

    public function clearCache(Tenant $tenant): void
    {
        Cache::forget("tenant_theme_{$tenant->id}");
    }
}
