<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Trainer extends Model
{
    protected $guarded=['id'];

    use HasFactory;


    /**
     * @return BelongsToMany
     */
    public function batches():BelongsToMany
    {
        return $this->belongsToMany(Batch::class,'trainer_batch');
    }

}
