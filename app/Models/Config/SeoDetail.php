<?php

namespace App\Models\Config;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoDetail extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'seo_details';
    protected $fillable = [
        'tenant_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'header_scripts',
        'body_scripts',
    ];
}
