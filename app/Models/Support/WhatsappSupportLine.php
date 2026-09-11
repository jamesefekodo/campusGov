<?php

namespace App\Models\Support;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappSupportLine extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'whatsapp_support_lines';
    protected $fillable = [
        'tenant_id',
        'name',
        'number',
        'status',
    ];
}
