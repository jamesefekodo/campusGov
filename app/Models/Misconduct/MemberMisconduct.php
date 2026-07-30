<?php

namespace App\Models\Misconduct;

use App\Models\Members\MemberPersonalProfile;
use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MemberMisconduct extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'member_misconducts';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'username',
        'case_id',
        'incident_date',
        'report_date',
        'reported_by',
        'reporter_username',
        'misconduct_type',
        'description',
        'witness_report',
        'disciplinary_action',
        'attachment_1',
        'attachment_2',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function ($misconduct) {
            if (empty($misconduct->uuid)) {
                $misconduct->uuid = 'misc-Y' . now()->format('Y') .
                    '-M' . now()->format('m') .
                    '-' . Str::uuid();
            }
        });
    }

    public function userProfile()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by', 'username');
    }
    public function memberPersonalProfile()
    {
        return $this->belongsTo(MemberPersonalProfile::class, 'username', 'username');
    }
}
