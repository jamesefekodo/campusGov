<?php

namespace App\Models\AcademicConfig;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Program extends Model
{
    use HasFactory;
    protected $table = 'programs';
    protected $fillable = [
        'uuid',
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

}
