<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\Theme\Frontend\FrontendTheme;
use App\Services\ThemeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThemeController extends Controller
{
    public function frontendThemes()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('themes.manage'),
            403
        );


        $tenant = tenant();


        $themes = FrontendTheme::where('is_active', true)
            ->orderBy('name')
            ->get();

        $tenantTheme = $tenant->themeSetting()->with('theme')->first();

        // The active theme is the tenant's selected theme,
        // or the platform default if none has been selected yet.
        $activeTheme = $tenantTheme?->theme
            ?? FrontendTheme::where('slug', 'matrix')->first();

        $activeColors = app(ThemeService::class)->forTenant($tenant);

        $data = [
            'themes' => $themes,
            'tenantTheme' => $tenantTheme,
            'activeTheme' => $activeTheme,
            'activeColors' => $activeColors,
        ];

        return view('users.admin.config.themes.frontend.frontendThemes', compact('data'));
    }
    public function selectTheme(FrontendTheme $frontendTheme)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('themes.manage'),
            403
        );

        abort_unless($frontendTheme->is_active, 404);

        $tenant = tenant();

        $tenant->themeSetting()->updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'frontend_theme_id' => $frontendTheme->id,
                'color_overrides' => [],
            ]
        );

        app(ThemeService::class)->clearCache($tenant);

        return redirect()
            ->route('frontendThemes')
            ->with('success', "{$frontendTheme->name} theme selected successfully.");
    }

    public function updateColors(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('themes.manage'),
            403
        );

        $tenant = tenant();

        $tenantTheme = $tenant->themeSetting()->with('theme')->first();

        $activeTheme = $tenantTheme?->theme
            ?? FrontendTheme::where('slug', 'matrix')
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'color_overrides' => ['required', 'array'],

            'color_overrides.primary' => [
                'required',
                'string',
                'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/',
            ],

            'color_overrides.secondary' => [
                'required',
                'string',
                'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/',
            ],

            'color_overrides.accent' => [
                'required',
                'string',
                'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/',
            ],

            'color_overrides.background' => [
                'required',
                'string',
                'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/',
            ],
        ]);

        $colorOverrides = [
            'primary' => strtoupper($validated['color_overrides']['primary']),
            'secondary' => strtoupper($validated['color_overrides']['secondary']),
            'accent' => strtoupper($validated['color_overrides']['accent']),
            'background' => strtoupper($validated['color_overrides']['background']),
        ];

        $tenant->themeSetting()->updateOrCreate(
            [
                'tenant_id' => $tenant->id,
            ],
            [
                'frontend_theme_id' => $activeTheme->id,
                'color_overrides' => $colorOverrides,
            ]
        );

        app(ThemeService::class)->clearCache($tenant);

        return redirect()
            ->route('frontendThemes')
            ->with('success', 'Theme colors updated successfully.');
    }

    public function resetTheme(FrontendTheme $frontendTheme)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('themes.manage'),
            403
        );

        abort_unless($frontendTheme->is_active, 404);

        $tenant = tenant();

        $tenantTheme = $tenant->themeSetting;

        if (! $tenantTheme || $tenantTheme->frontend_theme_id !== $frontendTheme->id) {
            return redirect()
                ->route('frontendThemes')
                ->with('error', 'You can only reset the currently active theme.');
        }

        $tenantTheme->update([
            'color_overrides' => [],
        ]);

        app(ThemeService::class)->clearCache($tenant);

        return redirect()
            ->route('frontendThemes')
            ->with('success', "{$frontendTheme->name} colors reset to its default.");
    }
}
