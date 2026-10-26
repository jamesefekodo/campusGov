<?php

namespace App\Models\Theme\Frontend;

use App\Models\Tenancy\Tenant;
use App\Models\Theme\Frontend\FrontendTheme;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TenantThemeSetting extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'theme_id',
        'color_overrides',
    ];

    protected $casts = [
        'color_overrides' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function theme()
    {
        return $this->belongsTo(FrontendTheme::class);
    }
}
