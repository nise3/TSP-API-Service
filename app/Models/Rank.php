<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Rank
 * @package App\Models
 * @property int|null organization_id
 * @property string title_en
 * @property string title_bn
 * @property int rank_type_id
 * @property-read Organization organization
 * @property-read RankType rankType
 */
class Rank extends BaseModel
{
    use ScopeRowStatusTrait;

    protected $guarded = ['id'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function rankType(): BelongsTo
    {
        return $this->belongsTo(RankType::class);
    }
}
