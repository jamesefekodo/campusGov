<?php

namespace App\Models\AcademicResources;

use App\Models\AcademicResources\PastQuestionContribution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PastQuestionContributionLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'contribution_id',
        'username',
    ];

    public function contribution()
    {
        return $this->belongsTo(
            PastQuestionContribution::class,
            'contribution_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'username',
            'username'
        );
    }
}
