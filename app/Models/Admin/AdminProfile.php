<?php

namespace App\Models\Admin;

use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminProfile extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'admin_profiles';
    protected $fillable = [
        'username',
        'name',
        'position',
        'description',
        'created_by',
        'last_update_by',
    ];

    public function userProfile()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'username');
    }
    public function lastUpdateBy()
    {
        return $this->belongsTo(User::class, 'last_update_by', 'username');
    }
}
