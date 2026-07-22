<?php

namespace App\Models\Members;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MemberPersonalProfile extends Model
{
    use HasFactory;
    protected $table = 'member_personal_profiles';
    protected $fillable = [
        'username',
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
}
