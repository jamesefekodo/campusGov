<?php

namespace App\Models\Config\AccessControl;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    // use BelongsToTenant;
    use HasFactory;
    protected $table = 'role_permissions';
    protected $fillable = [
        'tenant_id',
        'role_id',
        'permission_id',
    ];
    
}
