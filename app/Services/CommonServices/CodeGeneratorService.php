<?php

namespace App\Services\CommonServices;

use App\Models\Branch;
use App\Models\Course;
use App\Models\Institute;
use App\Models\SSPPessimisticLocking;
use App\Models\TrainingCenter;
use Faker\Provider\Uuid;
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
            $code = !empty($existingSSPCode) && !empty($existingSSPCode->code) ? $existingSSPCode->code : 0;
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
                $existingSSPCode->code = $code;
                $existingSSPCode->save();
            } else {
                SSPPessimisticLocking::create([
                    "code" => $code
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
            $instituteCode = Institute::findOrFail($sspId)->code;
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
            $instituteCode = Institute::findOrFail($sspId)->code;
            $trainingCenterExistingCode = TrainingCenter::where("institute_id", $sspId)->orderBy("id", "DESC")->first();
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
            $instituteCode = Institute::findOrFail($sspId)->code;
            $courseExistingCode = Course::where("institute_id", $sspId)->lockForUpdate()->orderBy("id", "DESC")->first();
            if (!empty($courseExistingCode) && !empty($courseExistingCode->code)) {
                $courseCode = explode(TrainingCenter::TRAINING_CENTER_CODE_PREFIX, $courseExistingCode->code);
                $courseCode = (int)sizeof($courseCode) > 1 ? end($courseCode) : time();
            } else {
                $courseCode = 0;
            }
            $courseCode = $courseCode + 1;
            $padLSize = TrainingCenter::TRAINING_CENTER_CODE_SIZE - strlen($courseCode);
            $code = str_pad($instituteCode . TrainingCenter::TRAINING_CENTER_CODE_PREFIX, $padLSize, '0') . $courseCode;
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

        return $code;
    }


}
