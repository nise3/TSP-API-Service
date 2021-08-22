<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trainer extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded=['id'];


    /**
     * @return BelongsToMany
     */
    public function batches():BelongsToMany
    {
        return $this->belongsToMany(Batch::class,'trainer_batch');
    }

}
