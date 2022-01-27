<?php

namespace App\Services\CommonServices;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Institute;
use App\Models\InvoicePessimisticLocking;
use App\Models\MerchantCodePessimisticLocking;
use App\Models\SSPPessimisticLocking;
use App\Models\TrainingCenter;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 *
 */
class CodeGeneratorService
{

    /**
     * @return string
     * @throws Throwable
     */
    public static function getSSPCode(): string
    {
        DB::beginTransaction();
        try {
            /** @var SSPPessimisticLocking $existingSSPCode */
            $existingSSPCode = SSPPessimisticLocking::lockForUpdate()->first();
            $code = !empty($existingSSPCode) && $existingSSPCode->last_incremental_value ? $existingSSPCode->last_incremental_value : 0;
            $code = $code + 1;
            $padSize = Institute::INSTITUTE_CODE_LENGTH - strlen($code);

            /**
             * Prefix+000000N. Ex: SSP0000001
             */
            $sspCode = str_pad(Institute::INSTITUTE_CODE_PREFIX, $padSize, '0', STR_PAD_RIGHT) . $code;

            /**
             * Code Update
             */
            if ($existingSSPCode) {
                $existingSSPCode->last_incremental_value = $code;
                $existingSSPCode->save();
            } else {
                SSPPessimisticLocking::create([
                    "last_incremental_value" => $code
                ]);
            }
            DB::commit();
            return $sspCode;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

    }


    /**
     * @param int $sspId
     * @return string
     * @throws Throwable
     */
    public static function getBranchCode(int $sspId): string
    {
        $instituteCode = Institute::find($sspId)->code;
        $branchExistingCode = Branch::where("institute_id", $sspId)->orderBy("id", "DESC")->first();
        if (!empty($branchExistingCode) && $branchExistingCode->code) {
            $branchCode = explode(Branch::BRANCH_CODE_PREFIX, $branchExistingCode->code);
            if (count($branchCode) > 1) {
                $branchCode = (int)end($branchCode);
            } else {
                $branchCode = 0;
            }
        } else {
            $branchCode = 0;
        }
        $branchCode = $branchCode + 1;
        $padLSize = Branch::BRANCH_CODE_SIZE - strlen($branchCode);
        return str_pad($instituteCode . Branch::BRANCH_CODE_PREFIX, $padLSize, '0') . $branchCode;
    }

    /**
     * @param int|null $sspId
     * @return string
     */
    public static function getTrainingCenterCode(int $sspId = null): string
    {
        [$instituteCode, $trainingCenterExistingCode] = !empty($sspId) ? self::getCode(TrainingCenter::class, $sspId) : self::getCode(TrainingCenter::class);

        if (!empty($trainingCenterExistingCode) && !empty($trainingCenterExistingCode->code)) {
            $trainingCenterCode = explode(TrainingCenter::TRAINING_CENTER_CODE_PREFIX, $trainingCenterExistingCode->code);
            if (sizeof($trainingCenterCode) > 1) {
                $trainingCenterCode = (int)end($trainingCenterCode);
            } else {
                $trainingCenterCode = 0;
            }
        } else {
            $trainingCenterCode = 0;
        }
        $trainingCenterCode = $trainingCenterCode + 1;
        $padLSize = TrainingCenter::TRAINING_CENTER_CODE_SIZE - strlen($trainingCenterCode);
        return str_pad($instituteCode . TrainingCenter::TRAINING_CENTER_CODE_PREFIX, $padLSize, '0') . $trainingCenterCode;
    }

    /**
     * @param int $sspId
     * @return string
     * @throws Throwable
     */
    public static function getCourseCode(int $sspId): string
    {
        [$instituteCode, $courseExistingCode] = !empty($sspId) ? self::getCode(Course::class, $sspId) : self::getCode(Course::class);

        if (!empty($courseExistingCode) && !empty($courseExistingCode->code)) {
            $courseCode = explode(Course::COURSE_CODE_PREFIX, $courseExistingCode->code);
            if (count($courseCode) > 1) {
                $courseCode = (int)end($courseCode);
            } else {
                $courseCode = 0;
            }
        } else {
            $courseCode = 0;
        }
        $courseCode = $courseCode + 1;
        $padLSize = Course::COURSE_CODE_SIZE - strlen($courseCode);
        return str_pad($instituteCode . Course::COURSE_CODE_PREFIX, $padLSize, '0') . $courseCode;
    }

    /**
     * @param int $courseId
     * @return string
     * @throws Throwable
     */
    public static function getBatchCode(int $courseId): string
    {
        $instituteCode = Course::find($courseId)->code;
        $batchExistingCode = Batch::where("course_id", $courseId)->withTrashed()->orderBy("id", "DESC")->first();
        if (!empty($batchExistingCode) && !empty($batchExistingCode->code)) {
            $batchCode = explode(Batch::BATCH_CODE_PREFIX, $batchExistingCode->code);
            if (count($batchCode) > 1) {
                $batchCode = (int)end($batchCode);
            } else {
                $batchCode = 0;
            }

        } else {
            $batchCode = 0;
        }
        $batchCode = $batchCode + 1;
        $padLSize = Batch::BATCH_CODE_SIZE - strlen($batchCode);
        return str_pad($instituteCode . Batch::BATCH_CODE_PREFIX, $padLSize, '0') . $batchCode;
    }

    /**
     * @throws Throwable
     */
    public static function getNewInvoiceCode(string $invoicePrefix, int $invoiceIdSize): string
    {
        $invoice = "";
        DB::beginTransaction();
        try {
            /** @var InvoicePessimisticLocking $existingSSPCode */
            $existingCode = InvoicePessimisticLocking::lockForUpdate()->first();
            $code = !empty($existingCode) && $existingCode->last_incremental_value ? $existingCode->last_incremental_value : 0;
            $code = $code + 1;
            $padSize = $invoiceIdSize - strlen($code);

            /**
             * Prefix+000000N. Ex: EN+Course Code+incremental number
             */
            $invoice = str_pad($invoicePrefix . "I", $padSize, '0', STR_PAD_RIGHT) . $code;

            /**
             * Code Update
             */
            if ($existingCode) {
                $existingCode->last_incremental_value = $code;
                $existingCode->save();
            } else {
                InvoicePessimisticLocking::create([
                    "last_incremental_value" => $code
                ]);
            }
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
        return $invoice;
    }


    /**
     * @throws Throwable
     */
    public static function getMerchantId(string $prefix, int $merchantIdSize): string
    {
        DB::beginTransaction();
        try {
            /** @var SSPPessimisticLocking $existingSSPCode */
            $existingCode = MerchantCodePessimisticLocking::lockForUpdate()->first();
            $code = !empty($existingCode) && $existingCode->last_incremental_value ? $existingCode->last_incremental_value : 0;
            $code = $code + 1;
            $padSize = $merchantIdSize - strlen($code);

            /**
             * Prefix+000000N. Ex: EN + - + incremental number
             */
            $merchantId = str_pad($prefix, $padSize, '0', STR_PAD_RIGHT) . $code;

            /**
             * Code Update
             */
            if ($existingCode) {
                $existingCode->last_incremental_value = $code;
                $existingCode->save();
            } else {
                MerchantCodePessimisticLocking::create([
                    "last_incremental_value" => $code
                ]);
            }
            DB::commit();
            return $merchantId;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

    }

    private static function getCode(string $model, int $id = null): array
    {
        /** @var User $authUser */
        $authUser = Auth::user();
        $queryAttribute = "institute_id";
        $queryAttributeValue = "";
        $code = "";
        if ($authUser->user_type == BaseModel::INDUSTRY_ASSOCIATION_USER_TYPE) {
            $queryAttribute = "industry_association_id";
            $queryAttributeValue = !empty($id) ? $id : $authUser->industry_association_id;
            $code = ServiceToServiceCall::getIndustryAssociationCode($queryAttributeValue);
        } else {
            $queryAttributeValue = !empty($id) ? $id : $authUser->institute_id;
            $code = Institute::find($queryAttributeValue)->code;
        }

        $existingCode = $model::where($queryAttribute, $queryAttributeValue)->withTrashed()->orderBy("id", "DESC")->first();

        return [
            $code,
            $existingCode
        ];
    }


}
