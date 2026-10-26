<?php

namespace App\Models\Theme\Frontend;

use Illuminate\Database\Eloquent\Model;

class FrontendTheme extends Model
{
    protected $table = 'frontend_themes';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'view_path',
        'is_active',
        'default_colors',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_colors' => 'array',
    ];
}
