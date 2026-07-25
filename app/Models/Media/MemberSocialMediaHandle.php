<?php

namespace App\Models\Media;

use App\Models\Media\RecognizedSocialMediaPlatform;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberSocialMediaHandle extends Model
{
    use HasFactory;
    protected $table = 'member_social_media_handles';
    protected $fillable = [
        'username',
        'platform',
        'handle',
        'link',
    ];

    public function thePlatform()
    {
        return $this->belongsTo(RecognizedSocialMediaPlatform::class, 'platform', 'slug');
    }
}
