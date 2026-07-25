<?php

namespace App\Models\Media;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecognizedSocialMediaPlatform extends Model
{
    use HasFactory;
    protected $table = 'recognized_social_media_platforms';
    protected $fillable = [
        'slug',
        'logo',
        'name',
        'decription',
        'is_active',
    ];
}
