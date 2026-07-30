<?php

namespace App\Models\AcademicConfig;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HeadOfFaculty extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'head_of_faculties';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'faculty',
        'name',
        'qualifications',
        'photo',
        'phone',
        'email',
        'profile',
        'is_active',
        'assumed_office_on',
        'tenure_end_on',
    ];

    // cast attributes
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean'
        ];
    }

    // uuid
    protected static function booted(): void
    {
        static::creating(function ($role) {
            if (empty($role->uuid)) {
                $role->uuid = 'fachd-' . Str::uuid();
            }
        });
    }

    public function hisFaculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty', 'slug');
    }
}
