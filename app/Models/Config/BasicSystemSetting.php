<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicSystemSetting extends Model
{
    use HasFactory;
    protected $table = 'basic_system_settings';
    protected $fillable = [
        'current_academic_year',
        'maintenance_mode',
        'association_name',
        'association_acronym',
        'association_logo',
        'association_slogan',
        'school_name',
        'school_acronym',
        'school_logo',
        'phone',
        'email',
        'address',
        'facebook_link',
        'instagram_link',
        'twitter_link',
    ];
}
