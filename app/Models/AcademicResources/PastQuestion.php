<?php

namespace App\Models\AcademicResources;

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use App\Models\Tenancy\Tenant;
use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PastQuestion extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'past_questions';

    protected $fillable = [
        'tenant_id',
        'uuid',
        'slug',
        'program',
        'program_type',
        'faculty',
        'department',
        'semester',
        'course_code',
        'course_title',
        'academic_year',
        'is_exam',
        'content',
        'image_1',
        'image_2',
        'image_3',
        'image_4',
        'image_5',
        'image_6',
        'created_by',
        'approved_by',
        'approval_status',
        'last_update',
        'last_update_by',
        'last_approval_update',
    ];

    protected $casts = [
        'last_update' => 'datetime',
        'last_approval_update' => 'datetime',
        'is_exam' => 'boolean',
        'academic_year' => 'integer',
    ];



    // uuid
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = 'pq-' . date('Ymd') . '-' . Str::uuid();
            }
        });
    }

    /**
     * The tenant that owns this publication.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by',
            'username'
        );
    }


    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'username');
    }

    public function lastUpdateBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_update_by', 'username');
    }
    public function programRelation()
    {
        return $this->belongsTo(
            Program::class,
            'program',
            'slug'
        );
    }
    public function facultyRelation()
    {
        return $this->belongsTo(
            Faculty::class,
            'faculty',
            'slug'
        );
    }
    public function departmentRelation()
    {
        return $this->belongsTo(
            Department::class,
            'department',
            'slug'
        );
    }
    public function programTypeRelation()
    {
        return $this->belongsTo(
            ProgramType::class,
            'program_type',
            'slug'
        );
    }
}
