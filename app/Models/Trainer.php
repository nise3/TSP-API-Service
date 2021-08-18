<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Trainer extends Model
{
    protected $guarded=['id'];


    /**
     * @return BelongsToMany
     */
    public function batches():BelongsToMany
    {
        return $this->belongsToMany(Batch::class,'trainer_batch');
    }

}
