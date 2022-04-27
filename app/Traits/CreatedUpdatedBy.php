<?php

namespace App\Traits;

/**
 * @method static updating(\Closure $param)
 * @method static creating(\Closure $param)
 */
trait CreatedUpdatedBy
{
    public static function bootCreatedUpdatedBy()
    {
        // updating created_by and updated_by when model is created
        static::creating(function ($model) {
            if(!empty(auth()->user())){
                if (!$model->isDirty('created_by')) {
                    $model->created_by = auth()->user()->id;
                }
                if (!$model->isDirty('updated_by')) {
                    $model->updated_by = auth()->user()->id;
                }
            }
        });

        // updating updated_by when model is updated
        static::updating(function ($model) {
            if(!empty(auth()->user())){
                if (!$model->isDirty('updated_by')) {
                    $model->updated_by = auth()->user()->id;
                }
            }
        });
    }
}
