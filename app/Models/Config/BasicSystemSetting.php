<?php

namespace App\Models\Config;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicSystemSetting extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'basic_system_settings';
    protected $fillable = [
        'tenant_id',
        'current_academic_year',
        'maintenance_mode',
        'association_name',
        'association_acronym',
        'association_logo',
        'association_slogan',
        'description',
        'operational_level',
        'school_name',
        'is_polytechnic',
        'school_acronym',
        'school_logo',
        'phone',
        'email',
        'address',
        'facebook_link',
        'instagram_link',
        'twitter_link',
        'primary_color',
        'secondary_color',
        'tertiary_color',
        'footer_plus',
        'school_founding_year',
        'account_balance',
    ];

    // caset attributes
    protected function casts(): array
    {
        return [
            'maintenance_mode' => 'boolean',
            'is_polytechnic' => 'boolean',
        ];
    }
}
