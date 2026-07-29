<?php

namespace App\Models\Finance\Dues;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AssociationDue extends Model
{
    use HasFactory;
    protected $table = 'association_dues';
    protected $fillable = [
        'uuid',
        'fee_id',
        'title',
        'description',
        'amount',
        'is_active',
        'academic_year',
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
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = 'due-Y' . now()->format('Y') .
                    '-M' . now()->format('m') .
                    '-' . Str::uuid();
            }
        });
    }

    public function variations()
    {
        return $this->hasMany(AssociationDueVariation::class, 'due_id', 'fee_id');
    }
}
