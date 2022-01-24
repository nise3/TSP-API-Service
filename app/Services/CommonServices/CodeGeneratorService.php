<?php

namespace App\Services\CommonServices;

use App\Models\Batch;
use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Institute;
use App\Models\InvoicePessimisticLocking;
use App\Models\MerchantCodePessimisticLocking;
use App\Models\SSPPessimisticLocking;
use App\Models\TrainingCenter;
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
        $sspCode = "";
        DB::beginTransaction();
        try {
            /** @var SSPPessimisticLocking $existingSSPCode */
            $existingSSPCode = SSPPessimisticLocking::lockForUpdate()->first();
            $code = !empty($existingSSPCode) && !empty($existingSSPCode->last_incremental_value) ? $existingSSPCode->last_incremental_value : 0;
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
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
        return $sspCode;
    }


    /**
     * @param int $sspId
     * @return string
     * @throws Throwable
     */
    public static function getBranchCode(int $sspId): string
    {
        DB::beginTransaction();
        $code = "";
        try {
            $instituteCode = Institute::find($sspId)->code;
            $branchExistingCode = Branch::where("institute_id", $sspId)->orderBy("id", "DESC")->first();
            if (!empty($branchExistingCode) && !empty($branchExistingCode->code)) {
                $branchCode = explode(Branch::BRANCH_CODE_PREFIX, $branchExistingCode->code);
                $branchCode = sizeof($branchCode) > 1 ? end($branchCode) : time();
            } else {
                $branchCode = 0;
            }
            $branchCode = $branchCode + 1;
            $padLSize = Branch::BRANCH_CODE_SIZE - strlen($branchCode);
            $code = str_pad($instituteCode . Branch::BRANCH_CODE_PREFIX, $padLSize, '0') . $branchCode;
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

        return $code;
    }

    /**
     * @param int $sspId
     * @return string
     * @throws Throwable
     */
    public static function getTrainingCenterCode(int $sspId): string
    {
        DB::beginTransaction();
        $code = "";
        try {
            $instituteCode = Institute::find($sspId)->code;
            $trainingCenterExistingCode = TrainingCenter::where("institute_id", $sspId)->withTrashed()->orderBy("id", "DESC")->first();
            if (!empty($trainingCenterExistingCode) && !empty($trainingCenterExistingCode->code)) {
                $trainingCenterCode = explode(TrainingCenter::TRAINING_CENTER_CODE_PREFIX, $trainingCenterExistingCode->code);
                $trainingCenterCode = (int)sizeof($trainingCenterCode) > 1 ? end($trainingCenterCode) : time();
            } else {
                $trainingCenterCode = 0;
            }
            $trainingCenterCode = $trainingCenterCode + 1;
            $padLSize = TrainingCenter::TRAINING_CENTER_CODE_SIZE - strlen($trainingCenterCode);
            $code = str_pad($instituteCode . TrainingCenter::TRAINING_CENTER_CODE_PREFIX, $padLSize, '0') . $trainingCenterCode;
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

        return $code;
    }

    /**
     * @param int $sspId
     * @return string
     * @throws Throwable
     */
    public static function getCourseCode(int $sspId): string
    {
        DB::beginTransaction();
        $code = "";
        try {
            $instituteCode = Institute::find($sspId)->code;
            $courseExistingCode = Course::where("institute_id", $sspId)->orderBy("id", "DESC")->first();
            if (!empty($courseExistingCode) && !empty($courseExistingCode->code)) {
                $courseCode = explode(Course::COURSE_CODE_PREFIX, $courseExistingCode->code);
                $courseCode = (int)sizeof($courseCode) > 1 ? end($courseCode) : time();
            } else {
                $courseCode = 0;
            }
            $courseCode = $courseCode + 1;
            $padLSize = Course::COURSE_CODE_SIZE - strlen($courseCode);
            $code = str_pad($instituteCode . Course::COURSE_CODE_PREFIX, $padLSize, '0') . $courseCode;
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

        return $code;
    }

    /**
     * @param int $courseId
     * @return string
     * @throws Throwable
     */
    public static function getBatchCode(int $courseId): string
    {
        DB::beginTransaction();
        $code = "";
        try {
            $instituteCode = Course::find($courseId)->code;
            $batchExistingCode = Batch::where("course_id", $courseId)->withTrashed()->orderBy("id", "DESC")->first();
            if (!empty($batchExistingCode) && !empty($batchExistingCode->code)) {
                $batchCode = explode(Batch::BATCH_CODE_PREFIX, $batchExistingCode->code);
                $batchCode = (int)sizeof($batchCode) > 1 ? end($batchCode) : time();
            } else {
                $batchCode = 0;
            }
            $batchCode = $batchCode + 1;
            $padLSize = Batch::BATCH_CODE_SIZE - strlen($batchCode);
            $code = str_pad($instituteCode . Batch::BATCH_CODE_PREFIX, $padLSize, '0') . $batchCode;
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

        return $code;
    }

    /**
     * @throws Throwable
     */
    public static function getInvoice(string $invoicePrefix,int $invoiceIdSize): string
    {
        $invoice = "";
        DB::beginTransaction();
        try {
            /** @var InvoicePessimisticLocking $existingSSPCode */
            $existingCode = InvoicePessimisticLocking::lockForUpdate()->first();
            $code = !empty($existingCode) && !empty($existingCode->last_incremental_value) ? $existingCode->last_incremental_value : 0;
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
        $merchantId = "";
        DB::beginTransaction();
        try {
            /** @var SSPPessimisticLocking $existingSSPCode */
            $existingCode = MerchantCodePessimisticLocking::lockForUpdate()->first();
            $code = !empty($existingCode) && !empty($existingCode->last_incremental_value) ? $existingCode->last_incremental_value : 0;
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
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
        return $merchantId;
    }

}
