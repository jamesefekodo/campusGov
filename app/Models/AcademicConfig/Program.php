<?php

namespace App\Models\AcademicConfig;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Program extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'programs';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'slug',
        'name',
        'short_name',
        'level_format',
        'numbering',
        'increment',
        'duration',
        'is_active',
        'award',
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
                $role->uuid = 'prog-' . Str::uuid();
            }
        });
    }

    public function programTypes()
    {
        return $this->hasMany(ProgramType::class, 'program_slug', 'slug');
    }
    public function durationOverrides()
    {
        return $this->hasMany(ProgramDurationOverride::class, 'program_slug', 'slug');
    }
}
