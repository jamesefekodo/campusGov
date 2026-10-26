<?php

namespace App\Models\Theme\Frontend;

use Illuminate\Database\Eloquent\Model;

class FrontendTheme extends Model
{
    protected $table = 'tenant_theme_settings';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'default_colors',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_colors' => 'array',
    ];
}
