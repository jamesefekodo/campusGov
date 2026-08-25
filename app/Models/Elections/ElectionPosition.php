<?php

namespace App\Models\Elections;

use App\Models\Elections\Election;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ElectionPosition extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'election_positions';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'election_id',
        'position',
        'description',
        'application_fee',
    ];


    // uuid
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = 'elpo-' . date('Ymd') . '-' . Str::uuid();
            }
        });
    }

    public function election()
    {
        return $this->belongsTo(Election::class, 'election_id', 'election_id');
    }
    public function candidateGroups()
    {
        return $this->hasMany(ElectionPositionQualifiedCandidateGroup::class, 'position_id', 'id');
    }
    public function qualifiedCandidates()
    {
        return $this->hasMany(
            ElectionCandidateApplication::class,
            'position_id',
            'id'
        )->where('status', 'approved');
    }

    public function highestVoteCandidate()
    {
        $candidates = $this->qualifiedCandidates()
            ->with([
                'election',
                'theCandidate.memberPersonalProfile',
            ])
            ->get();

        if ($candidates->isEmpty()) {
            return [
                'winner' => null,
                'is_tie' => false,
                'highest_votes' => 0,
                'tied_candidates' => collect(),
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | Calculate votes
    |--------------------------------------------------------------------------
    */

        if ($this->election->voting_method === 'monetary') {

            $candidates = $candidates->map(function ($candidate) {

                $candidate->voteCount = $candidate->totalMonetaryVote();

                return $candidate;
            });
        } else {

            $voteCounts = ElectionStandardVote::where(
                'election_id',
                $this->election_id
            )
                ->where('position', $this->id)
                ->selectRaw('candidate, COUNT(*) as votes')
                ->groupBy('candidate')
                ->pluck('votes', 'candidate');

            $candidates = $candidates->map(function ($candidate) use ($voteCounts) {

                $candidate->voteCount = $voteCounts->get(
                    $candidate->application_id,
                    0
                );

                return $candidate;
            });
        }

        /*
    |--------------------------------------------------------------------------
    | Find highest vote
    |--------------------------------------------------------------------------
    */

        $highestVotes = $candidates->max('voteCount');

        /*
    |--------------------------------------------------------------------------
    | No votes
    |--------------------------------------------------------------------------
    */

        if ($highestVotes <= 0) {
            return [
                'winner' => null,
                'is_tie' => false,
                'highest_votes' => 0,
                'tied_candidates' => collect(),
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | Find candidates with highest votes
    |--------------------------------------------------------------------------
    */

        $tiedCandidates = $candidates
            ->where('voteCount', $highestVotes)
            ->values();

        /*
    |--------------------------------------------------------------------------
    | Tie
    |--------------------------------------------------------------------------
    */

        if ($tiedCandidates->count() > 1) {
            return [
                'winner' => null,
                'is_tie' => true,
                'highest_votes' => $highestVotes,
                'tied_candidates' => $tiedCandidates,
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | Single winner
    |--------------------------------------------------------------------------
    */

        return [
            'winner' => $tiedCandidates->first(),
            'is_tie' => false,
            'highest_votes' => $highestVotes,
            'tied_candidates' => collect(),
        ];
    }
}
