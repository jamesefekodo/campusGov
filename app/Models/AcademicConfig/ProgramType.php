<?php

namespace App\Models\AcademicConfig;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProgramType extends Model
{
    use HasFactory;
    protected $table = 'program_types';
    protected $fillable = [
        'uuid',
        'slug',
        'program_slug',
        'type_name',
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
                $role->uuid = 'progtp-' . Str::uuid();
            }
        });
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'slug', 'program_slug');
    }
}
