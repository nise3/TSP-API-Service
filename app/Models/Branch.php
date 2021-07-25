<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Branch
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property int institute_id
 * @property int row_status
 * @property string|null address
 * @property string|null google_map_src
 */

class Branch extends BaseModel
{

    protected $guarded = ['id'];

    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }
}
