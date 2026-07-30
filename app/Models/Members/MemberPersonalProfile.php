<?php

namespace App\Models\Members;

use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MemberPersonalProfile extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'member_personal_profiles';
    protected $fillable = [
        'username',
        'tenant_id',
        'phone1',
        'email1',
        'phone2',
        'email2',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'dob',
        'nationality',
        'state_of_origin',
        'lga_of_origin',
        'address1',
        'address2',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    // cast attributes
    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean'
        ];
    }
    public function userProfile()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by', 'username');
    }
}
