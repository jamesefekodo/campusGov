<?php

namespace App\Models\AcademicResources;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class IndustryAffiliateTag extends Pivot
{
    protected $table = 'industry_affiliate_tags';

    protected $fillable = [
        'industry_affiliate_id',
        'tag_for_industry_id',
    ];

    public $incrementing = true;

    public function industryAffiliate(): BelongsTo
    {
        return $this->belongsTo(
            IndustryAffiliate::class,
            'industry_affiliate_id'
        );
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(
            TagsForIndustryAffiliate::class,
            'tag_for_industry_id'
        );
    }
}