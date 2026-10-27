<?php

namespace App\Services;

use App\Models\Tenancy\Tenant;
use App\Models\Theme\Frontend\FrontendTheme;
use Illuminate\Support\Facades\Cache;

class ThemeService
{
    public function activeTheme(Tenant $tenant): ?FrontendTheme
    {
        $themeId = Cache::remember(
            "tenant_active_theme_id_{$tenant->id}",
            now()->addHours(24),
            function () use ($tenant) {
                $setting = $tenant->themeSetting()->with('theme')->first();

                $theme = $setting?->theme
                    ?? FrontendTheme::where('slug', 'matrix')
                    ->where('is_active', true)
                    ->first();

                return $theme?->id;
            }
        );

        if (! $themeId) {
            return null;
        }

        return FrontendTheme::find($themeId);
    }


    public function homepageView(Tenant $tenant): string
    {
        $theme = $this->activeTheme($tenant);

        $view = $theme?->view_path ?? 'users.guests.homepage';

        // Fallback if the selected theme’s homepage view is missing.
        if (! view()->exists($view)) {
            return 'users.guests.homepage';
        }

        return $view;
    }

    public function forTenant(Tenant $tenant): array
    {
        return Cache::remember(
            "tenant_theme_{$tenant->id}",
            now()->addHours(24),
            function () use ($tenant) {
                $setting = $tenant->themeSetting()->with('theme')->first();

                if ($setting && $setting->theme) {
                    return array_merge(
                        $setting->theme->default_colors ?? [],
                        $setting->color_overrides ?? []
                    );
                }

                $defaultTheme = FrontendTheme::where('slug', 'matrix')
                    ->where('is_active', true)
                    ->first();

                return $defaultTheme?->default_colors ?? [];
            }
        );
    }

    public function clearCache(Tenant $tenant): void
    {
        Cache::forget("tenant_active_theme_{$tenant->id}");
        Cache::forget("tenant_theme_{$tenant->id}");
    }

    public function view(Tenant $tenant, string $page): string
    {
        $theme = $this->activeTheme($tenant);

        $basePath = $theme?->view_path;

        if ($basePath) {
            // Your DB stores the homepage view as the theme path.
            // Remove ".homepage" so other pages can be resolved.
            $basePath = preg_replace('/\.homepage$/', '', $basePath);

            $themeView = "{$basePath}.{$page}";

            if (view()->exists($themeView)) {
                return $themeView;
            }
        }

        return "users.guests.{$page}";
    }
}
