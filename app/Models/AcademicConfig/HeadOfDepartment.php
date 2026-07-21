<?php

namespace App\Models\AcademicConfig;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HeadOfDepartment extends Model
{
    use HasFactory;
    protected $table = 'head_of_departments';
    protected $fillable = [
        'uuid',
        'slug',
        'department',
        'name',
        'administrative_head_title',
        'description',
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
                $role->uuid = 'hod-' . Str::uuid();
            }
        });
    }

    public function hisDepartment()
    {
        return $this->belongsTo(Department::class, 'department', 'slug');
    }
}
