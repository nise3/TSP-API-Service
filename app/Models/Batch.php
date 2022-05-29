<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Batch
 * @package App\Models
 * @property int id
 * @property int institute_id
 * @property int branch_id
 * @property int training_center_id
 * @property int course_id
 * @property int row_status
 * @property int number_of_seats
 * @property int available_seats
 * @property Carbon registration_start_date
 * @property Carbon registration_end_date
 * @property Carbon batch_start_date
 * @property Carbon batch_end_date
 * @property Carbon result_published_at
 * @property Carbon result_processed_at
 * @property-read Institute institute
 * @property-read Branch branch
 * @property-read TrainingCenter trainingCenter
 * @property-read Course course
 * @property-read Collection examTypes
 */
class Batch extends BaseModel
{
    use ScopeRowStatusTrait, SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    public const BATCH_CODE_PREFIX = "BT";
    public const BATCH_CODE_SIZE = 28;

    // TODO: This method should be checked . It gives error.

//    public function toArray(): array
//    {
//        $originalData = parent::toArray();
//        $authUser = Auth::user();
//
//        if ($authUser && Auth::user()->isIndustryAssociationUser() || !empty($originalData['industry_association_id'])) {
//            $this->getIndustryAssociationData($originalData);
//        }
//        return $originalData;
//    }


    /**
     * @return BelongsTo
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'batch_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class, 'batch_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'batch_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function trainingCenter(): BelongsTo
    {
        return $this->belongsTo(TrainingCenter::class, 'batch_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function trainers(): BelongsToMany
    {
        return $this->belongsToMany(Trainer::class, 'trainer_batch', 'batch_id', 'trainer_id');
    }

    public function examTypes(): BelongsToMany
    {
        return $this->belongsToMany(ExamType::class, 'batch_exams',  'batch_id', 'exam_type_id');
    }
    public function CertificateTemplateIds(): BelongsToMany
    {
        return $this->belongsToMany(ExamType::class, 'batch_certificate_templates',  'batch_id', 'certificate_template_id');
    }

}
