<?php

namespace App\Models\AcademicConfig;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProgramDurationOverride extends Model
{
    use HasFactory;
    protected $table = 'program_duration_overrides';
    protected $fillable = [
        'uuid',
        'program_slug',
        'program_type_slug',
        'department_slug',
        'duration',
        'is_active',
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
                $role->uuid = 'durexc-' . Str::uuid();
            }
        });
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'slug', 'program_slug');
    }
    public function department()
    {
        return $this->belongsTo(Program::class, 'slug', 'department_slug');
    }
}
