<?php

namespace App\Models\AcademicResources;

use App\Models\AcademicResources\PastQuestionContributionLike;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PastQuestionContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'past_question_id',
        'username',
        'content',
        'edited_at',
    ];

    protected $casts = [
        'edited_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (PastQuestionContribution $contribution) {
            if (!$contribution->uuid) {
                $contribution->uuid = (string) Str::uuid();
            }
        });
    }

    public function pastQuestion()
    {
        return $this->belongsTo(
            PastQuestion::class
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'username',
            'username'
        );
    }

    public function likes()
    {
        return $this->hasMany(
            PastQuestionContributionLike::class,
            'contribution_id'
        );
    }

    public function getLikesCountAttribute(): int
    {
        return $this->likes_count ?? $this->likes()->count();
    }

    public function getCreatorNameAttribute(): string
    {
        $profile = $this->creator?->memberPersonalProfile;

        if ($profile) {
            return trim(
                $profile->first_name . ' ' .
                    ($profile->middle_name
                        ? $profile->middle_name . ' '
                        : '') .
                    $profile->last_name
            );
        }

        return $this->username ?? 'Unknown user';
    }
}
