<?php

namespace App\Models\Config;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSlider extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'homepage_slider';
    protected $fillable = [
        'tenant_id',
        'background_image',
        'header',
        'order',
        'description',
    ];

    // caset attributes
    // protected function casts(): array
    // {
    //     return [
    //         'maintenance_mode' => 'boolean',
    //         'is_polytechnic' => 'boolean',
    //     ];
    // }
}
