<?php

namespace App\Models\AcademicResources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndustryAffiliateTag extends Model
{
    protected $table = 'industry_affiliate_tags';
    protected $fillable = [
        'industry_affiliate_id',
        'tag_for_industry_id',
    ];

    /**
     * Because your pivot migration includes an "id" column.
     */
    public $incrementing = true;

    /**
     * The publication connected to this tag.
     */
    public function industryAffiliates(): BelongsTo
    {
        return $this->belongsTo(
            IndustryAffiliate::class,
            'industry_affiliate_id'
        );
    }

    /**
     * The tag connected to this publication.
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(
            TagsForIndustryAffiliate::class,
            'industry_affiliate_id'
        );
    }
}
