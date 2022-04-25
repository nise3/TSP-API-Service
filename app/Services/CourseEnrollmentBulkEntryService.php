<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\EduBoard;
use App\Models\EducationLevel;
use App\Models\EduGroup;
use App\Models\EnrollmentEducation;
use App\Models\EnrollmentGuardian;
use App\Models\EnrollmentProfessionalInfo;
use App\Models\ExamDegree;
use App\Models\LocDistrict;
use App\Models\LocDivision;
use App\Models\LocUpazila;
use App\Models\PaymentTransactionHistory;
use App\Models\PhysicalDisability;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CourseEnrollmentBulkEntryService
{
    const GENDER_COLUMN = 'F1';
    const RELIGION_COLUMN = 'G1';
    const MARITAL_STATUS_COLUMN = 'H1';
    const NATIONALITY = 'I1';
    const FREEDOM_FIGHTER = 'J1';
    const PHYSICAL_DISABILITY_STATUS_COLUMN = 'K1';
    const PHYSICAL_DISABILITIES = 'L1';
    const ETHNIC_GROUP_COLUMN = 'M1';
    const IDENTITY_NUMBER_TYPE_COLUMN = "N1";
    const DIVISION_COLUMN = "P1";
    const DISTRICT_COLUMN = "Q1";
    const UPAZILA_COLUMN = "R1";

    const EXAM_DEGREE_ID = "exam_degree_id";
    const EXAM_DEGREE_NAME = "exam_degree_name";
    const EDU_BOARD_COLUMN = "edu_board_id";
    const DURATION = "duration";
    const YEAR_OF_PASSING = "year_of_passing";
    const PASSING_YEAR = "passing_year";
    const CGPA_SCALE = "cgpa_scale";
    const RESULT = "result";
    const EDU_GROUP_COLUMN = "edu_group_id";
    const EDU_INFO = "EDU_INFO";
    const OCCUPATION_INFO = "occupation_info";
    const IS_CURRENTLY_EMPLOYEE = 'is_currently_employed';
    const YOUTH_SKILLS = 'skills';

    const YOUTH_PROFILE_BASIC_FIELDS = [
        1 => [
            "attribute" => "first_name",
            "label" => "First Name",
            "column" => "A1"
        ],
        2 => [
            "attribute" => "last_name",
            "label" => "Last Name",
            "column" => "B1",
        ],
        3 => [
            'attribute' => 'email',
            'label' => 'Email',
            'column' => 'C1',
        ],
        4 => [
            "attribute" => 'mobile',
            "label" => 'mobile',
            'column' => 'D1',
        ],
        6 => [
            "attribute" => 'date_of_birth',
            "label" => "Date Of Birth",
            'column' => 'E1',
        ],
        7 => [
            'attribute' => 'gender',
            'label' => "Gender",
            'column' => self::GENDER_COLUMN
        ],
        8 => [
            'attribute' => 'religion',
            'label' => "Religion",
            'column' => self::RELIGION_COLUMN
        ],
        9 => [
            'attribute' => 'marital_status',
            'label' => "Marital Status",
            'column' => self::MARITAL_STATUS_COLUMN,
        ],
        10 => [
            'attribute' => 'nationality',
            'label' => 'nationality',
            'column' => self::NATIONALITY
        ],
        11 => [
            'attribute' => 'freedom_fighter_status',
            'label' => 'Freedom Fighter Status',
            'column' => self::FREEDOM_FIGHTER
        ],
        12 => [
            'attribute' => 'physical_disability_status',
            'label' => 'Physical Disability Status',
            'column' => self::PHYSICAL_DISABILITY_STATUS_COLUMN
        ],
        13 => [
            'attribute' => 'physical_disabilities',
            'label' => 'Physical Disabilities',
            'column' => self::PHYSICAL_DISABILITIES
        ],
        14 => [
            'attribute' => 'does_belong_to_ethnic_group',
            'label' => 'Does Belong to Ethnic Group',
            'column' => self::ETHNIC_GROUP_COLUMN
        ],
        15 => [
            "attribute" => 'identity_number_type',
            "label" => 'Identity Number Type',
            'column' => self::IDENTITY_NUMBER_TYPE_COLUMN
        ],
        16 => [
            "attribute" => 'identity_number',
            "label" => "Identity Number",
            'column' => 'O1'
        ],
        17 => [
            "attribute" => 'loc_division_id',
            "label" => "Loc Division Id",
            'column' => self::DIVISION_COLUMN
        ],
        18 => [
            "attribute" => 'loc_district_id',
            "label" => "Loc District Id",
            'column' => self::DISTRICT_COLUMN
        ],
        19 => [
            "attribute" => 'loc_upazila_id',
            "label" => "Loc Upazila Id",
            'column' => self::UPAZILA_COLUMN
        ],
        20 => [
            "attribute" => 'village_or_area',
            "label" => "Village Or Area",
            'column' => 'S1'
        ],
        21 => [
            "attribute" => 'house_n_road',
            "label" => "House N Road",
            'column' => 'T1'
        ],
        22 => [
            "attribute" => 'zip_or_postal_code',
            "label" => "Zip Or Postal Code",
            'column' => 'U1'
        ]
    ];

    const PROFESSIONAL_INFO = [
        [
            "attribute" => 'main_profession',
            "label" => "Main Profession",
            'column' => ''
        ],
        [
            "attribute" => 'other_profession',
            "label" => "Other Profession",
            'column' => ''
        ],
        [
            "attribute" => 'monthly_income',
            "label" => "Monthly Income",
            'column' => ''
        ],
        [
            "attribute" => self::IS_CURRENTLY_EMPLOYEE,
            "label" => "Is Currently Employed",
            'column' => ''
        ],
        [
            "attribute" => 'years_of_experiences',
            "label" => "Years Of Experiences",
            'column' => ''
        ],
        [
            "attribute" => self::PASSING_YEAR,
            "label" => "Passing Year",
            'column' => ''
        ]

    ];
    const FATHER_MOTHER_INFO = [
        [
            "attribute" => "father_name",
            "label" => "Father Name",
            "column" => ""
        ],
        [
            "attribute" => "father_nid",
            "label" => "Father Nid",
            "column" => ""
        ],
        [
            "attribute" => "father_mobile",
            "label" => "Father Mobile",
            "column" => ""
        ],
        [
            "attribute" => "father_date_of_birth",
            "label" => "Father Date Of Birth",
            "column" => ""
        ],
        [
            "attribute" => "mother_name",
            "label" => "Mother Name",
            "column" => ""
        ],
        [
            "attribute" => "mother_nid",
            "label" => "Mother Nid",
            "column" => ""
        ],
        [
            "attribute" => "mother_mobile",
            "label" => "Mother Mobile",
            "column" => ""
        ],
        [
            "attribute" => "mother_date_of_birth",
            "label" => "Mother Date Of Birth",
            "column" => ""
        ]
    ];

    const PSC_JSC = [
        [
            "attribute" => self::EXAM_DEGREE_ID,
            "label" => "Exam Degree Id",
            "column" => ""
        ],
        [
            "attribute" => "major_or_concentration",
            "label" => "Major Or Concentration",
            "column" => ""
        ],
        [
            "attribute" => self::EDU_BOARD_COLUMN,
            "label" => "Educational Board",
            "column" => ""
        ],
        [
            "attribute" => "institute_name",
            "label" => "Institute Name",
            "column" => ""
        ],
        [
            "attribute" => self::RESULT,
            "label" => "Result",
            "column" => ""
        ],
        [
            "attribute" => "marks_in_percentage",
            "label" => "Marks in Percentage",
            "column" => ""
        ],
        [
            "attribute" => self::CGPA_SCALE,
            "label" => "Cgpa Scale",
            "column" => ""
        ],
        [
            "attribute" => "cgpa",
            "label" => "Cgpa",
            "column" => ""
        ],
        [
            "attribute" => self::YEAR_OF_PASSING,
            "label" => "Year of Passing",
            "column" => ""
        ],
        [
            "attribute" => self::DURATION,
            "label" => "Duration",
            "column" => ""
        ]
    ];

    const SSC_HSC = [
        [
            "attribute" => self::EXAM_DEGREE_ID,
            "label" => "Exam Degree Id",
            "column" => ""
        ],
        [
            "attribute" => "major_or_concentration",
            "label" => "Major Or Concentration",
            "column" => ""
        ],
        [
            "attribute" => self::EDU_GROUP_COLUMN,
            "label" => "Educational Group",
            "column" => ""
        ],
        [
            "attribute" => self::EDU_BOARD_COLUMN,
            "label" => "Educational Board",
            "column" => ""
        ],
        [
            "attribute" => "institute_name",
            "label" => "Institute Name",
            "column" => ""
        ],
        [
            "attribute" => self::RESULT,
            "label" => "Result",
            "column" => ""
        ],
        [
            "attribute" => "marks_in_percentage",
            "label" => "Marks in Percentage",
            "column" => ""
        ],
        [
            "attribute" => self::CGPA_SCALE,
            "label" => "Cgpa Scale",
            "column" => ""
        ],
        [
            "attribute" => "cgpa",
            "label" => "Cgpa",
            "column" => ""
        ],
        [
            "attribute" => self::YEAR_OF_PASSING,
            "label" => "Year of Passing",
            "column" => ""
        ],
        [
            "attribute" => self::DURATION,
            "label" => "Duration",
            "column" => ""
        ]

    ];
    const DIPLOMA = [
        [
            "attribute" => self::EXAM_DEGREE_ID,
            "label" => "Exam Degree Id",
            "column" => ""
        ],
        [
            "attribute" => "major_or_concentration",
            "label" => "Major Or Concentration",
            "column" => ""
        ],
        [
            "attribute" => self::EDU_GROUP_COLUMN,
            "label" => "Educational Group",
            "column" => ""
        ],
        [
            "attribute" => "institute_name",
            "label" => "Institute Name",
            "column" => ""
        ],
        [
            "attribute" => self::RESULT,
            "label" => "Result",
            "column" => ""
        ],
        [
            "attribute" => "marks_in_percentage",
            "label" => "Marks in Percentage",
            "column" => ""
        ],
        [
            "attribute" => self::CGPA_SCALE,
            "label" => "Cgpa Scale",
            "column" => ""
        ],
        [
            "attribute" => "cgpa",
            "label" => "Cgpa",
            "column" => ""
        ],
        [
            "attribute" => self::YEAR_OF_PASSING,
            "label" => "Year of Passing",
            "column" => ""
        ],
        [
            "attribute" => self::DURATION,
            "label" => "Duration",
            "column" => ""
        ]

    ];

    const BACHELOR_MASTERS = [
        [
            "attribute" => self::EXAM_DEGREE_ID,
            "label" => "Exam Degree Id",
            "column" => ""
        ],
        [
            "attribute" => "major_or_concentration",
            "label" => "Major Or Concentration",
            "column" => ""
        ],
        [
            "attribute" => "institute_name",
            "label" => "Institute Name",
            "column" => ""
        ],
        [
            "attribute" => self::RESULT,
            "label" => "Result",
            "column" => ""
        ],
        [
            "attribute" => "marks_in_percentage",
            "label" => "Marks in Percentage",
            "column" => ""
        ],
        [
            "attribute" => self::CGPA_SCALE,
            "label" => "Cgpa Scale",
            "column" => ""
        ],
        [
            "attribute" => "cgpa",
            "label" => "Cgpa",
            "column" => ""
        ],
        [
            "attribute" => self::YEAR_OF_PASSING,
            "label" => "Year of Passing",
            "column" => ""
        ],
        [
            "attribute" => self::DURATION,
            "label" => "Duration",
            "column" => ""
        ]
    ];
    const PHD = [
        [
            "attribute" => self::EXAM_DEGREE_NAME,
            "label" => "Exam Degree Name",
            "column" => ""
        ],
        [
            "attribute" => "major_or_concentration",
            "label" => "Major Or Concentration",
            "column" => ""
        ],
        [
            "attribute" => "institute_name",
            "label" => "Institute Name",
            "column" => ""
        ],
        [
            "attribute" => self::RESULT,
            "label" => "Result",
            "column" => ""
        ],
        [
            "attribute" => self::YEAR_OF_PASSING,
            "label" => "Year of Passing",
            "column" => ""
        ],
        [
            "attribute" => self::DURATION,
            "label" => "Duration",
            "column" => ""
        ],
        [
            "attribute" => "achievements",
            "label" => "Achievements",
            "column" => ""
        ]
    ];

    const DYNAMIC_COLUMN_LABEL = [
        "V1", "W1", "X1", "Y1", "Z1", "AA1", "AB1", "AC1",
        "AD1", "AE1", "AF1", "AG1", "AH1", "AI1", "AJ1", "AK1", "AL1", "AM1", "AN1",
        "AO1", "AP1", "AQ1", "AR1", "AS1", "AT1", "AU1", "AV1", "AW1", "AX1", "AY1", "AZ1",
        "BA1", "BB1", "BC1", "BD1", "BE1", "BF1", "BG1", "BH1", "BI1", "BJ1",
        "BK1", "BL1", "BM1", "BN1", "BO1", "BP1", "BQ1", "BR1", "BS1", "BT1", "BU1",
        "BV1", "BW1", "BX1", "BY1", "BZ1",
        "CA1", "CB1", "CC1", "CD1", "CE1", "CF1", "CG1", "CH1", "CI1", "CJ1",
        "CK1", "CL1", "CM1", "CN1", "CO1", "CP1", "CQ1", "CR1", "CS1", "CT1", "CU1",
        "CV1", "CW1", "CX1", "CY1", "CZ1",
        "DA1", "DB1", "DC1", "DD1", "DE1", "DF1", "DG1", "DH1", "DI1", "DJ1",
        "DK1", "DL1", "DM1", "DN1", "DO1", "DP1", "DQ1", "CD1", "DS1", "DT1", "DU1",
        "DV1", "DW1", "DX1", "DY1", "DZ1",
    ];

    const EXAM_TYPE_PSC = "psc";
    const EXAM_TYPE_JSC = "jsc";
    const EXAM_TYPE_SSC = "ssc";
    const EXAM_TYPE_HSC = "hsc";
    const EXAM_TYPE_DIPLOMA = "diploma";
    const EXAM_TYPE_HONOURS = "honors";
    const EXAM_TYPE_MASTERS = "masters";
    const EXAM_TYPE_PHD = "phd";

    const EXAM_LEVEL_EXAM_TYPE_WISE = [
        self::EXAM_TYPE_PSC => EducationLevel::EDUCATION_LEVEL_PSC_5_PASS,
        self::EXAM_TYPE_JSC => EducationLevel::EDUCATION_LEVEL_JSC_JDC_8_PASS,
        self::EXAM_TYPE_SSC => EducationLevel::EDUCATION_LEVEL_SECONDARY,
        self::EXAM_TYPE_HSC => EducationLevel::EDUCATION_LEVEL_HIGHER_SECONDARY,
        self::EXAM_TYPE_DIPLOMA => EducationLevel::EDUCATION_LEVEL_DIPLOMA,
        self::EXAM_TYPE_HONOURS => EducationLevel::EDUCATION_LEVEL_BACHELOR,
        self::EXAM_TYPE_MASTERS => EducationLevel::EDUCATION_LEVEL_MASTERS,
        self::EXAM_TYPE_PHD => EducationLevel::EDUCATION_LEVEL_PHD,
    ];


    public function purseExcelAndInsert(array $data, array $extraData): array
    {
        $this->explodeData($data);
        $validatedData = [];
        foreach ($data as $key => $datum) {
            $payload = [];
            $professionalInfo = $this->parseProfessionalInfo($datum);
            $guardianInfo = $this->parseGuardianInfo($datum);
            [$educationInfo, $examTypePrefix] = $this->parseEducationInfo($datum);
            $addressInfo = $this->parseAddressInfoInfo($datum);
            $payload = array_merge($this->parseCourseEnrollmentInfo($datum), $addressInfo['present_address']);

            $payload['physical_disabilities'] = $datum['physical_disabilities'];
            $payload['address_info'] = $addressInfo;
            $payload['address_info']['is_permanent_address'] = BaseModel::FALSE;
            $payload['professional_info'] = $professionalInfo;
            $payload['guardian_info'] = $guardianInfo;
            $payload['education_info'] = $educationInfo;
            $payload = array_merge($extraData, $payload);
            $validatedData[$key] = $this->courseEnrollmentBulkDataValidator($payload, $key + 2)->validate();
        }
        return $validatedData;
    }

    private function parseCourseEnrollmentInfo(array $data): array
    {
        $courseEnrollmentInfo = new CourseEnrollment();
        $courseEnrollmentInfo->fill($data);
        return $courseEnrollmentInfo->attributesToArray();
    }

    private function parseAddressInfoInfo(array $data): array
    {
        $addressInfo['present_address']['loc_division_id'] = $data['loc_division_id'];
        $addressInfo['present_address']['loc_district_id'] = $data['loc_district_id'];
        $addressInfo['present_address']['loc_upazila_id'] = $data['loc_upazila_id'];
        $addressInfo['present_address']['village_or_area'] = $data['village_or_area'];
        $addressInfo['present_address']['house_n_road'] = $data['house_n_road'];
        $addressInfo['present_address']['zip_or_postal_code'] = $data['zip_or_postal_code'];
        return $addressInfo;
    }

    private function parseEducationInfo(array $data): array
    {
        $educationInfo = [];
        $educationLevelId = 0;
        $prefixHeader = [];
        foreach ($data as $key => $value) {
            $explode = explode("_", $key);
            if (sizeof($explode) > 0 && in_array($explode[0], array_keys(self::EXAM_LEVEL_EXAM_TYPE_WISE))) {
                $replacePrefix = $explode[0] . "_";
                $attribute = str_replace($replacePrefix, "", $key);
                $educationLevelId = $this->getEducationLevelId($explode[0]);
                $prefixHeader[$key] = $replacePrefix;
                $educationInfo[$educationLevelId][$attribute] = $value;
                $educationInfo[$educationLevelId]['is_foreign_institute'] = BaseModel::FALSE;
            }
        }

        $this->getFilterValueResultWise($educationInfo);
        return [
            $educationInfo,
            $prefixHeader
        ];
    }

    private function parseGuardianInfo(array $data): array
    {
        $guardianInfo = new EnrollmentGuardian();
        $guardianInfo->fill($data);
        return $guardianInfo->attributesToArray();
    }

    private function parseProfessionalInfo(array $data): array
    {
        $professionalInfo = new EnrollmentProfessionalInfo();
        $professionalInfo->fill($data);
        return $professionalInfo->attributesToArray();
    }

    private function explodeData(array &$data): void
    {
        foreach ($data as $mainKey => $value) {
            foreach ($value as $subKey => $subValue) {
                $explode = explode('|', $subValue);
                if (sizeof($explode) == 2 && !empty($explode[0])) {
                    $explodedValue = trim($explode[0]);
                    if (is_numeric($explodedValue)) {
                        $explodedValue = (int)$explodedValue;
                    }
                    $data[$mainKey][$subKey] = $explodedValue;
                }
            }

        }
    }


    /**
     * @throws Exception
     */
    public function buildImportExcel(int $courseId, int $batchId): string
    {
        $objPHPExcel = new Spreadsheet();
        foreach (self::YOUTH_PROFILE_BASIC_FIELDS as $key => $value) {
            $columnLabel = ucfirst($this->getColumnLabel($value['attribute']));
            $column = $value['column'];
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column, $columnLabel);
            $objPHPExcel->getActiveSheet()->getColumnDimension(substr_replace($column, "", -1))->setWidth(strlen($columnLabel) + 10);
            $objPHPExcel->getActiveSheet()->getStyle($column)->getNumberFormat()->setFormatCode('@');
           // $objPHPExcel->getActiveSheet()->freezePane($column);
        }

        $gender = "";
        foreach (BaseModel::GENDER_LABEL as $key => $value) {
            $gender .= $key . " | " . $value . ",";
        }
        $religion = "";
        foreach (CourseEnrollment::RELIGION_LABEL as $key => $value) {
            $religion .= $key . " | " . $value . ",";
        }
        $maritalStatus = "";
        foreach (CourseEnrollment::MARITAL_STATUS_LABEL as $key => $value) {
            $maritalStatus .= $key . " | " . $value . ",";
        }
        $freedomFighter = "";
        foreach (CourseEnrollment::FREEDOM_FIGHTER_STATUS_LABEL as $key => $value) {
            $freedomFighter .= $key . " | " . $value . ",";
        }
        $nationality = "";
        foreach (config('nise3.nationalities') as $key => $value) {
            $nationality .= $key . " | " . $value['bn'] . ",";
        }
        $physicalDisabilityDropDown = "";
        foreach (PhysicalDisability::all() as $value) {
            $physicalDisabilityDropDown .= $value->id . " | " . $value->title . ",";
        }
        $identityNumberTypeDropdown = "";
        foreach (BaseModel::IDENTITY_TYPES as $key => $type) {
            $identityNumberTypeDropdown .= $key . ' | ' . $type . ",";
        }


        $dynamicField = self::YOUTH_PROFILE_BASIC_FIELDS;

        $this->buildProfessionalInfo($objPHPExcel, $dynamicField, $courseId);
        $this->buildGuardianInfo($objPHPExcel, $dynamicField, $courseId);
        $this->buildEducationInfo($objPHPExcel, $dynamicField, $courseId);


        $objPHPExcel->createSheet(1);
        $divisionIncrement = 1;
        foreach (LocDivision::all() as $locDivision) {
            $objPHPExcel->setActiveSheetIndex(1)
                ->setCellValue('A' . $divisionIncrement++, $locDivision->id . ' | ' . $locDivision->title);
        }

        $objPHPExcel->createSheet(1);
        $districtIncrement = 1;
        foreach (LocDistrict::all() as $locDistrict) {
            $objPHPExcel->setActiveSheetIndex(1)
                ->setCellValue('B' . $districtIncrement++, $locDistrict->id . ' | ' . $locDistrict->title);
        }

        $objPHPExcel->createSheet(1);
        $upazilaIncrement = 1;
        foreach (LocUpazila::all() as $locUpazila) {
            $objPHPExcel->setActiveSheetIndex(1)
                ->setCellValue('C' . $upazilaIncrement++, $locUpazila->id . ' | ' . $locUpazila->title);
        }

        $this->dropDownColumnBuilder($objPHPExcel, self::GENDER_COLUMN, $gender);
        $this->dropDownColumnBuilder($objPHPExcel, self::MARITAL_STATUS_COLUMN, $maritalStatus);
        $this->dropDownColumnBuilder($objPHPExcel, self::RELIGION_COLUMN, $religion);
        $this->dropDownColumnBuilder($objPHPExcel, self::NATIONALITY, $nationality);
        $this->dropDownColumnBuilder($objPHPExcel, self::FREEDOM_FIGHTER, $freedomFighter);
        $this->dropDownColumnBuilder($objPHPExcel, self::PHYSICAL_DISABILITY_STATUS_COLUMN, $this->getTrueFalse());
        $this->dropDownColumnBuilder($objPHPExcel, self::PHYSICAL_DISABILITIES, $physicalDisabilityDropDown);
        $this->dropDownColumnBuilder($objPHPExcel, self::ETHNIC_GROUP_COLUMN, $this->getTrueFalse());
        $this->dropDownColumnBuilder($objPHPExcel, self::IDENTITY_NUMBER_TYPE_COLUMN, $identityNumberTypeDropdown);


        $this->dropDownColumnBuilderWorkSheetBased($objPHPExcel, self::DIVISION_COLUMN, '$A:$A', 1);
        $this->dropDownColumnBuilderWorkSheetBased($objPHPExcel, self::DISTRICT_COLUMN, '$B:$B', 2);
        $this->dropDownColumnBuilderWorkSheetBased($objPHPExcel, self::UPAZILA_COLUMN, '$C:$C', 3);


        $fileName = $this->getFilePath($courseId, $batchId);
//        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        header("Content-Disposition: attachment;filename=$fileName");
//        header('Cache-Control: max-age=0');
//        header('Cache-Control: max-age=1');
//        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
//        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
//        header('Cache-Control: cache, must-revalidate');
//        header('Pragma: public');
        $writer = new Xlsx($objPHPExcel);
        ob_start();
        $writer->save('php://output');
        $excelData = ob_get_contents();
        ob_end_clean();
        return "data:application/vnd.ms-excel;base64,".base64_encode($excelData);
    }

    /**
     * @throws Exception
     */
    private function dropDownColumnBuilder(Spreadsheet $objPHPExcel, string $column, string $dropdownData)
    {
        $objValidation = $objPHPExcel->setActiveSheetIndex(0)->getCell($column)->getDataValidation();
        $objValidation->setType(DataValidation::TYPE_LIST);
        $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $objValidation->setAllowBlank(false);
        $objValidation->setShowInputMessage(true);
        $objValidation->setShowDropDown(true);
        $objValidation->setPromptTitle('Pick from list');
        $objValidation->setPrompt('Please pick a value from the drop-down list.');
        $objValidation->setErrorTitle('Input error');
        $objValidation->setError('Value is not in list');
        $objValidation->setFormula1('"' . $dropdownData . '"');
    }

    /**
     * @throws Exception
     */
    private function dropDownColumnBuilderWorkSheetBased(Spreadsheet $objPHPExcel, string $column, string $dropdownData, string $worksheetNumber)
    {
        $objValidation = $objPHPExcel->setActiveSheetIndex(0)->getCell($column)->getDataValidation();
        $objValidation->setType(DataValidation::TYPE_LIST);
        $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $objValidation->setAllowBlank(false);
        $objValidation->setShowInputMessage(true);
        $objValidation->setShowDropDown(true);
        $objValidation->setPromptTitle('Pick from list');
        $objValidation->setPrompt('Please pick a value from the drop-down list.');
        $objValidation->setErrorTitle('Input error');
        $objValidation->setError('Value is not in list');
        $f2 = $dropdownData;
        $objValidation->setFormula1("='Worksheet {$worksheetNumber}'!{$f2}");

    }

    /**
     * @throws Exception
     */
    private function buildEducationInfo(Spreadsheet $objPHPExcel, array &$dynamicField, int $courseId)
    {
        if ($this->courseConfig(self::EXAM_TYPE_PSC, $courseId)) {
            $this->generateEducationInfo($objPHPExcel, self::PSC_JSC, $dynamicField, self::EXAM_TYPE_PSC);
        }
        if ($this->courseConfig(self::EXAM_TYPE_JSC, $courseId)) {
            $this->generateEducationInfo($objPHPExcel, self::PSC_JSC, $dynamicField, self::EXAM_TYPE_JSC);
        }
        if ($this->courseConfig(self::EXAM_TYPE_SSC, $courseId)) {
            $this->generateEducationInfo($objPHPExcel, self::SSC_HSC, $dynamicField, self::EXAM_TYPE_SSC);
        }
        if ($this->courseConfig(self::EXAM_TYPE_HSC, $courseId)) {
            $this->generateEducationInfo($objPHPExcel, self::SSC_HSC, $dynamicField, self::EXAM_TYPE_HSC);
        }
        if ($this->courseConfig(self::EXAM_TYPE_DIPLOMA, $courseId)) {
            $this->generateEducationInfo($objPHPExcel, self::DIPLOMA, $dynamicField, self::EXAM_TYPE_DIPLOMA);
        }
        if ($this->courseConfig(self::EXAM_TYPE_HONOURS, $courseId)) {
            $this->generateEducationInfo($objPHPExcel, self::BACHELOR_MASTERS, $dynamicField, self::EXAM_TYPE_HONOURS);
        }
        if ($this->courseConfig(self::EXAM_TYPE_MASTERS, $courseId)) {
            $this->generateEducationInfo($objPHPExcel, self::BACHELOR_MASTERS, $dynamicField, self::EXAM_TYPE_MASTERS);
        }
        if ($this->courseConfig(self::EXAM_TYPE_PHD, $courseId)) {
            $this->generateEducationInfo($objPHPExcel, self::PHD, $dynamicField, self::EXAM_TYPE_PHD);
        }
    }

    /**
     * @throws Exception
     */
    private function generateEducationInfo(Spreadsheet $objPHPExcel, array $info, array &$dynamicField, string $columnPrefix)
    {
        $index = $this->getIndex($dynamicField);
        foreach ($info as $value) {
            if (!empty(self::DYNAMIC_COLUMN_LABEL[$index])) {
                $column = self::DYNAMIC_COLUMN_LABEL[$index];

                $columnLabel = ucfirst($columnPrefix . " " . $this->getColumnLabel($value['attribute']));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column, $columnLabel);
                $objPHPExcel->getActiveSheet()->getColumnDimension(substr_replace($column, "", -1))->setWidth(strlen($columnLabel) + 10);

                $this->dropdownFormula($objPHPExcel, $value, $column, $columnPrefix);

                $value['attribute'] = strtolower($columnPrefix) . "_" . $value['attribute'];
                $value['label'] = ucfirst($columnPrefix) . " " . $this->getColumnLabel($value['attribute']);
                $value['column'] = $column;
                $lastIndexKeyOfDynamicField = array_key_last($dynamicField) + 1;
                $dynamicField[$lastIndexKeyOfDynamicField] = $value;
            }
            $index++;
        }
    }


    /**
     * @throws Exception
     */
    private function buildGuardianInfo(Spreadsheet $objPHPExcel, array &$dynamicField, int $courseId)
    {
        $index = $this->getIndex($dynamicField);
        if ($this->courseConfig(self::EDU_INFO, $courseId)) {
            foreach (self::FATHER_MOTHER_INFO as $value) {
                if (!empty(self::DYNAMIC_COLUMN_LABEL[$index])) {
                    $column = self::DYNAMIC_COLUMN_LABEL[$index];

                    $columnLabel = ucfirst($this->getColumnLabel($value['attribute']));
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column, $columnLabel);
                    $objPHPExcel->getActiveSheet()->getColumnDimension(substr_replace($column, "", -1))->setWidth(strlen($columnLabel) + 10);

                    $value['column'] = $column;
                    $lastIndexKeyOfDynamicField = array_key_last($dynamicField) + 1;
                    $dynamicField[$lastIndexKeyOfDynamicField] = $value;
                }
                $index++;
            }
        }
    }

    /**
     * @throws Exception
     */
    private function buildProfessionalInfo(Spreadsheet $objPHPExcel, array &$dynamicField, int $courseId)
    {
        $index = $this->getIndex($dynamicField);
        if ($this->courseConfig(self::OCCUPATION_INFO, $courseId)) {
            foreach (self::PROFESSIONAL_INFO as $value) {
                if (!empty(self::DYNAMIC_COLUMN_LABEL[$index])) {
                    $column = self::DYNAMIC_COLUMN_LABEL[$index];

                    $columnLabel = ucfirst($this->getColumnLabel($value['attribute']));
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column, $columnLabel);
                    $objPHPExcel->getActiveSheet()->getColumnDimension(substr_replace($column, "", -1))->setWidth(strlen($columnLabel) + 10);

                    $this->dropdownFormula($objPHPExcel, $value, $column);

                    $value['column'] = $column;
                    $lastIndexKeyOfDynamicField = array_key_last($dynamicField) + 1;
                    $dynamicField[$lastIndexKeyOfDynamicField] = $value;
                }
                $index++;
            }
        }
    }

    private function courseConfig(string $type, int $courseId): bool
    {
        $courseConfig = Course::findOrFail($courseId)->application_form_settings;
        $courseConfig = json_decode($courseConfig, true);
        if ($type == self::EXAM_TYPE_PSC) {
            if (!empty($courseConfig['psc_passing_info'])) {
                return $courseConfig['psc_passing_info'][0] && $courseConfig['psc_passing_info'][1];
            }
        } elseif ($type == self::EXAM_TYPE_JSC) {
            if (!empty($courseConfig['jsc_passing_info'])) {
                return $courseConfig['jsc_passing_info'][0] && $courseConfig['jsc_passing_info'][1];
            }
        } elseif ($type == self::EXAM_TYPE_SSC) {
            if (!empty($courseConfig['ssc_passing_info'])) {
                return $courseConfig['ssc_passing_info'][0] && $courseConfig['ssc_passing_info'][1];
            }
        } elseif ($type == self::EXAM_TYPE_HSC) {
            if (!empty($courseConfig['hsc_passing_info'])) {
                return $courseConfig['hsc_passing_info'][0] && $courseConfig['hsc_passing_info'][1];
            }
        } elseif ($type == self::EXAM_TYPE_DIPLOMA) {
            if (!empty($courseConfig['diploma_passing_info'])) {
                return $courseConfig['diploma_passing_info'][0] && $courseConfig['diploma_passing_info'][1];
            }
        } elseif ($type == self::EXAM_TYPE_HONOURS) {
            if (!empty($courseConfig['honors_passing_info'])) {
                return $courseConfig['honors_passing_info'][0] && $courseConfig['honors_passing_info'][1];
            }
        } elseif ($type == self::EXAM_TYPE_MASTERS) {
            if (!empty($courseConfig['masters_passing_info'])) {
                return $courseConfig['masters_passing_info'][0] && $courseConfig['masters_passing_info'][1];
            }
        } elseif ($type == self::EXAM_TYPE_PHD) {
            if (!empty($courseConfig['phd_passing_info'])) {
                return $courseConfig['phd_passing_info'][0] && $courseConfig['phd_passing_info'][1];
            }
        } elseif ($type == self::EDU_INFO) {
            if (!empty($courseConfig['education_info'])) {
                return $courseConfig['education_info'][0] && $courseConfig['education_info'][1];
            }
        } elseif ($type == self::OCCUPATION_INFO) {
            if (!empty($courseConfig['occupation_info'])) {
                return $courseConfig['occupation_info'][0] && $courseConfig['occupation_info'][1];
            }
        }
        return false;
    }

    private function getIndex(array $dynamicField): int
    {
        $dynamicFieldLastValue = end($dynamicField)['column'];
        $index = array_search($dynamicFieldLastValue, self::DYNAMIC_COLUMN_LABEL, true);
        if ($index) {
            return $index + 1;
        } else {
            return 0;
        }
    }

    private function getTrueFalse(): string
    {
        $trueFalse = [
            BaseModel::TRUE => "True",
            BaseModel::FALSE => "False"
        ];
        $trueFalseDropDown = "";
        foreach ($trueFalse as $key => $value) {
            $trueFalseDropDown .= $key . " | " . $value . ",";
        }
        return $trueFalseDropDown;
    }

    /**
     * @throws Exception
     */
    private function dropdownFormula(Spreadsheet $objPHPExcel, mixed $value, string $column, string $columnPrefix = null)
    {
        $dropDownValues = $this->getExamDegrees();
        $dropDownValues[self::RESULT] = $this->getResultType();
        $dropDownValues[self::EDU_GROUP_COLUMN] = $this->getEduGroup();
        $dropDownValues[self::EDU_BOARD_COLUMN] = $this->getEducationBoard();
        $dropDownValues[self::YEAR_OF_PASSING] = $this->getPassingYear();
        $dropDownValues[self::PASSING_YEAR] = $this->getPassingYear();
        $dropDownValues[self::IS_CURRENTLY_EMPLOYEE] = $this->getTrueFalse();
        Log::info(json_encode($value));
        $attribute = $value['attribute'];
        if ($value['attribute'] == self::EXAM_DEGREE_ID) {
            $attribute = $columnPrefix . "_" . $value['attribute'];
        }
        if (!empty($dropDownValues[$attribute])) {

            $dropDownData = $dropDownValues[$attribute];
            $this->dropDownColumnBuilder($objPHPExcel, $column, $dropDownData);
        }

    }

    private
    function getEducationBoard(): string
    {
        $eduBoard = "";
        foreach (EduBoard::all() as $value) {
            $eduBoard .= $value->id . " | " . $value->title . ",";
        }
        return $eduBoard;
    }

    private
    function getEduGroup(): string
    {
        $eduGroup = "";
        foreach (EduGroup::all() as $value) {
            $eduGroup .= $value->id . " | " . $value->title . ",";;
        }
        return $eduGroup;
    }

    private
    function getResultType(): string
    {
        $eduGroup = "";
        foreach (config("nise3.exam_degree_results") as $value) {
            $eduGroup .= $value['id'] . " | " . $value['title'] . ",";
        }
        return $eduGroup;
    }

    private
    function getPassingYear(): string
    {
        $passingYear = "";
        $startingYear = 1972;
        $endingYear = (int)date("Y");
        for ($i = $endingYear; $i >= $startingYear; $i--) {
            $passingYear .= $i . ",";
        }
        return $passingYear;
    }

    #[
        ArrayShape(["psc_exam_degree_id" => "string", "jsc_exam_degree_id" => "string", "ssc_exam_degree_id" => "string", "hsc_exam_degree_id" => "string", "diploma_exam_degree_id" => "string", "honours_exam_degree_id" => "string", "masters_exam_degree_id" => "string"])]
    private function getExamDegrees(): array
    {

        $educationDegreePSC = "";
        $educationDegreeJSC = "";
        $educationDegreeSSC = "";
        $educationDegreeHSC = "";
        $educationDegreeDiploma = "";
        $educationDegreeGraduate = "";
        $educationDegreeMasters = "";

        $educationDegrees = ExamDegree::select([
            "exam_degrees.id",
            "exam_degrees.title",
            "exam_degrees.title_en",
            "education_levels.code as level_of_education",
        ])->join("education_levels", "education_levels.id", "exam_degrees.education_level_id")
            ->get();

        foreach ($educationDegrees as $value) {
            if ($value->level_of_education == EducationLevel::EDUCATION_LEVEL_PSC_5_PASS) {
                $educationDegreePSC .= $value->id . " | " . $value->title . ",";
            }
            if ($value->level_of_education == EducationLevel::EDUCATION_LEVEL_JSC_JDC_8_PASS) {
                $educationDegreeJSC .= $value->id . " | " . $value->title . ",";
            }
            if ($value->level_of_education == EducationLevel::EDUCATION_LEVEL_SECONDARY) {
                $educationDegreeSSC .= $value->id . " | " . $value->title . ",";
            }
            if ($value->level_of_education == EducationLevel::EDUCATION_LEVEL_HIGHER_SECONDARY) {
                $educationDegreeHSC .= $value->id . " | " . $value->title . ",";
            }
            if ($value->level_of_education == EducationLevel::EDUCATION_LEVEL_DIPLOMA) {
                $educationDegreeDiploma .= $value->id . " | " . $value->title . ",";
            }
            if ($value->level_of_education == EducationLevel::EDUCATION_LEVEL_BACHELOR) {
                $educationDegreeGraduate .= $value->id . " | " . $value->title . ",";
            }
            if ($value->level_of_education == EducationLevel::EDUCATION_LEVEL_MASTERS) {
                $educationDegreeMasters .= $value->id . " | " . $value->title . ",";
            }

        }

        return [
            "psc_exam_degree_id" => $educationDegreePSC,
            "jsc_exam_degree_id" => $educationDegreeJSC,
            "ssc_exam_degree_id" => $educationDegreeSSC,
            "hsc_exam_degree_id" => $educationDegreeHSC,
            "diploma_exam_degree_id" => $educationDegreeDiploma,
            "honors_exam_degree_id" => $educationDegreeGraduate,
            "masters_exam_degree_id" => $educationDegreeMasters
        ];
    }

    private function getFilePath(int $courseId, int $batchId): string
    {
        $courseTitle = Course::findOrFail($courseId)->title;
        $batchTitle = Batch::findOrFail($batchId)->title;
        $filePath = Storage::disk('public')->path('');
        $filePath = $filePath . "/" . $batchTitle . " " . $courseTitle . " bulk import.xlsx";
        return preg_replace('/\s+/', '-', $filePath);
    }

    private function getEducationLevelId(string $examType)
    {
        $code = self::EXAM_LEVEL_EXAM_TYPE_WISE[$examType];
        return EducationLevel::where("code", $code)->firstOrFail()->id;
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function courseEnrollmentBulkDataValidator(array $data, int $rowNumber): \Illuminate\Contracts\Validation\Validator
    {
        $data['deleted_at'] = null;
        if (!empty($data["physical_disabilities"])) {
            $data["physical_disabilities"] = isset($data['physical_disabilities']) && is_array($data['physical_disabilities']) ? $data['physical_disabilities'] : explode(',', $data['physical_disabilities']);
        }

        $customMessage = [
            "course_id.unique" => "Course is already enrolled.[62000]",
            "required" => "The :attribute in row " . $rowNumber . " is required.[50000]",
            "string" => "The :attribute in row " . $rowNumber . " must be a string.[60000]",
            "integer" => "The :attribute in row " . $rowNumber . " must be a integer.[32000]",
            "int" => "The :attribute in row " . $rowNumber . " must be a integer.[32000]",
            "number" => "The :attribute in row " . $rowNumber . " must be a number.[46000]",
            "in" => "The selected :attribute in row " . $rowNumber . " is invalid.[30000]",
            "date" => "The selected :attribute in row " . $rowNumber . " is invalid date.[14000]"
        ];

        $rules = [
            'first_name' => [
                'required',
                'string',
                'max:300'
            ],
            'first_name_en' => [
                'nullable',
                'string',
                'max:150'
            ],
            'last_name' => [
                'required',
                'string',
                'max:300'
            ],
            'last_name_en' => [
                'nullable',
                'string',
                'max:150'
            ],
            'program_id' => [
                'nullable',
                'exists:programs,id,deleted_at,NULL',
                'int'
            ],
            'course_id' => [
                'required',
                'exists:courses,id,deleted_at,NULL',
                'int',
                'min:1',
                Rule::unique("course_enrollments", "course_id")
                    ->where(function (Builder $query) use ($data) {
                        return $query->where("batch_id", $data['batch_id'])
                            ->where('mobile', $data['mobile']);
                    })
            ],
            'training_center_id' => [
                'nullable',
                'exists:training_centers,id,deleted_at,NULL',
                'int',
                'min:1'
            ],
            'batch_id' => [
                'nullable',
                'exists:batches,id,deleted_at,NULL',
                'int',
                'min:1'
            ],
            'gender' => [
                'required',
                Rule::in(BaseModel::GENDERS),
                'int',
            ],
            'date_of_birth' => [
                'required',
                'date',
                function ($attr, $value, $failed) {
                    if (Carbon::parse($value)->greaterThan(Carbon::now()->subYear(5))) {
                        $failed('Age should be greater than 5 years.');
                    }
                }
            ],
            'email' => [
                'required',
                'email',
            ],
            "mobile" => [
                "required",
                "max:11",
                BaseModel::MOBILE_REGEX
            ],
            'marital_status' => [
                'required',
                'int',
                Rule::in(CourseEnrollment::MARITAL_STATUSES)
            ],
            'religion' => [
                'required',
                'int',
                Rule::in(CourseEnrollment::RELIGIONS)
            ],
            'nationality' => [
                'int',
                'required'
            ],
            'does_belong_to_ethnic_group' => [
                'nullable',
                'int',
                Rule::in([BaseModel::TRUE, BaseModel::FALSE])
            ],
            'identity_number_type' => [
                'int',
                'nullable',
                Rule::in(CourseEnrollment::IDENTITY_TYPES)
            ],
            'identity_number' => [
                'nullable'
            ],
            'freedom_fighter_status' => [
                'int',
                'nullable',
                Rule::in(CourseEnrollment::FREEDOM_FIGHTER_STATUSES)
            ],
            'passport_photo_path' => [
                'string',
                'nullable',
            ],
            'signature_image_path' => [
                'string',
                'nullable',
            ],
            "physical_disability_status" => [
                "nullable",
                "int",
                Rule::in([BaseModel::TRUE, BaseModel::FALSE])
            ],
            'address_info' => [
                'nullable',
                'array',
                'min:1'
            ],
            'address_info.present_address' => [
                Rule::requiredIf(!empty($data['address_info'])),
                'nullable',
                'array',
            ],
            'address_info.present_address.loc_division_id' => [
                Rule::requiredIf(!empty($data['address_info']['present_address'])),
                'nullable',
                'integer',
            ],
            'address_info.present_address.loc_district_id' => [
                Rule::requiredIf(!empty($data['address_info']['present_address'])),
                'nullable',
                'integer',
            ],
            'address_info.present_address.loc_upazila_id' => [
                'nullable',
                'integer',
            ],
            'address_info.present_address.village_or_area' => [
                'nullable',
                'max:500',
                'min:2'
            ],
            'address_info.present_address.village_or_area_en' => [
                'nullable',
                'max:250',
                'min:2'
            ],
            'address_info.present_address.house_n_road' => [
                'nullable',
                'max:500',
                'min:2'
            ],
            'address_info.present_address.house_n_road_en' => [
                'nullable',
                'max:250',
                'min:2'
            ],
            'address_info.present_address.zip_or_postal_code' => [
                'nullable',
                'max:5',
                'min:4'
            ],

            'address_info.is_permanent_address' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['address_info']);
                }),
                'nullable',
                'int',
                Rule::in([BaseModel::TRUE, BaseModel::FALSE])
            ],

            'address_info.permanent_address' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['address_info']['is_permanent_address']) && $data['address_info']['is_permanent_address'] == BaseModel::TRUE;
                }),
                'nullable',
                'array',
                'min:1'
            ],
            'address_info.permanent_address.loc_division_id' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['address_info']['is_permanent_address']) && $data['address_info']['is_permanent_address'] == BaseModel::TRUE;
                }),
                'nullable',
                'integer',
            ],
            'address_info.permanent_address.loc_district_id' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['address_info']['is_permanent_address']) && $data['address_info']['is_permanent_address'] == BaseModel::TRUE && !empty($data['address_info']['permanent_address']);
                }),
                'nullable',
                'integer',
            ],
            'address_info.permanent_address.loc_upazila_id' => [
                'nullable',
                'integer',
            ],
            'address_info.permanent_address.village_or_area' => [
                'nullable',
                'max:500',
                'min:2'
            ],
            'address_info.permanent_address.village_or_area_en' => [
                'nullable',
                'max:250',
                'min:2'
            ],
            'address_info.permanent_address.house_n_road' => [
                'nullable',
                'max:500',
                'min:2'
            ],
            'address_info.permanent_address.house_n_road_en' => [
                'nullable',
                'max:250',
                'min:2'
            ],
            'address_info.permanent_address.zip_or_postal_code' => [
                'nullable',
                'max:5',
                'min:4'
            ],
            "professional_info" => [
                'nullable',
                'array',
                'min:1'
            ],
            'professional_info.main_profession' => [
                Rule::requiredIf(!empty($data['professional_info'])),
                'nullable',
                'string',
                'max:500'
            ],
            'professional_info.main_profession_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'professional_info.other_profession' => [
                'nullable',
                'string',
                'max:500'
            ],
            'professional_info.other_profession_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'professional_info.monthly_income' => [
                Rule::requiredIf(!empty($data['professional_info'])),
                'nullable',
                'numeric'
            ],
            'professional_info.is_currently_employed' => [
                Rule::requiredIf(!empty($data['professional_info'])),
                'nullable',
                'int',
                Rule::in([BaseModel::FALSE, BaseModel::TRUE])
            ],
            'professional_info.years_of_experiences' => [
                Rule::requiredIf(!empty($data['professional_info'])),
                'nullable',
                'int'
            ],
            "guardian_info" => [
                'nullable',
                'array',
                'min:1'
            ],
            'guardian_info.father_name' => [
                Rule::requiredIf(!empty($data['guardian_info'])),
                'nullable',
                'string',
                'max:500'
            ],
            'guardian_info.father_name_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'guardian_info.father_nid' => [
                'nullable',
                'max:30'
            ],
            'guardian_info.father_mobile' => [
                'nullable',
                'max:11',
                BaseModel::MOBILE_REGEX
            ],
            'guardian_info.father_date_of_birth' => [
                'nullable',
                'date',
                function ($attr, $value, $failed) {
                    if (Carbon::parse($value)->greaterThan(Carbon::now()->subYear(25))) {
                        $failed('Age should be greater than 25 years.');
                    }
                }
            ],
            'guardian_info.mother_name' => [
                Rule::requiredIf(!empty($data['guardian_info'])),
                'nullable',
                'string',
                'max:500'
            ],
            'guardian_info.mother_name_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'guardian_info.mother_nid' => [
                'nullable',
                'max:30'
            ],
            'guardian_info.mother_mobile' => [
                'nullable',
                'max:11',
                BaseModel::MOBILE_REGEX
            ],
            'guardian_info.mother_date_of_birth' => [
                'nullable',
                'date',
                function ($attr, $value, $failed) {
                    if (Carbon::parse($value)->greaterThan(Carbon::now()->subYear(25))) {
                        $failed('Age should be greater than 25 years.');
                    }
                }
            ],
            "miscellaneous_info" => [
                'nullable',
                'array',
                'min:1'
            ],
            'miscellaneous_info.has_own_family_home' => [
                Rule::requiredIf(!empty($data['miscellaneous_info'])),
                'nullable',
                'int',
                Rule::in([BaseModel::TRUE, BaseModel::FALSE])
            ],
            'miscellaneous_info.has_own_family_land' => [
                Rule::requiredIf(!empty($data['miscellaneous_info'])),
                'nullable',
                'int',
                Rule::in([BaseModel::TRUE, BaseModel::FALSE])
            ],
            'miscellaneous_info.number_of_siblings' => [
                'nullable',
                'int',
            ],
            'miscellaneous_info.recommended_by_any_organization' => [
                Rule::requiredIf(!empty($data['miscellaneous_info'])),
                'nullable',
                'int',
                Rule::in([BaseModel::TRUE, BaseModel::FALSE])
            ],
            'education_info' => [
                'nullable',
                'array',
            ],
        ];
        if (!empty($data['education_info'])) {
            foreach ($data['education_info'] as $eduLabelId => $fields) {
                $validationField = 'education_info.' . $eduLabelId . '.';
                $rules[$validationField . 'exam_degree_id'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $data) {
                        return app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::DEGREE, $eduLabelId);
                    }),
                    'nullable',
                    'int',
                    'exists:exam_degrees,id,deleted_at,NULL,education_level_id,' . $eduLabelId
                ];
                $rules[$validationField . 'exam_degree_name'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $data) {
                        return app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::EXAM_DEGREE_NAME, $eduLabelId);
                    }),
                    'nullable',
                    "string"
                ];
                $rules[$validationField . 'exam_degree_name_en'] = [
                    "nullable",
                    "string"
                ];
                $rules[$validationField . 'major_or_concentration'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $data) {
                        return app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::MAJOR, $eduLabelId);
                    }),
                    'nullable',
                    "string"
                ];
                $rules[$validationField . 'major_or_concentration_en'] = [
                    "nullable",
                    "string"
                ];
                $rules[$validationField . 'edu_group_id'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $data) {
                        return app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::EDU_GROUP, $eduLabelId);
                    }),
                    'nullable',
                    'exists:edu_groups,id,deleted_at,NULL',
                    "integer"
                ];
                $rules[$validationField . 'edu_board_id'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $data) {
                        return app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::BOARD, $eduLabelId);
                    }),
                    'nullable',
                    'exists:edu_boards,id,deleted_at,NULL',
                    "integer"
                ];
                $rules[$validationField . 'institute_name'] = [
                    'required',
                    'string',
                    'max:800',
                ];
                $rules[$validationField . 'institute_name_en'] = [
                    'nullable',
                    'string',
                    'max:400',
                ];
                $rules[$validationField . 'is_foreign_institute'] = [
                    'required',
                    'integer',
                    Rule::in([BaseModel::TRUE, BaseModel::FALSE])
                ];
                $rules[$validationField . 'foreign_institute_country_id'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        return BaseModel::TRUE == !empty($fields['is_foreign_institute']) ? $fields['is_foreign_institute'] : BaseModel::FALSE;
                    }),
                    'nullable',
                    "integer"
                ];
                $rules[$validationField . 'result'] = [
                    "required",
                    "integer",
                    Rule::in(array_keys(config("nise3.exam_degree_results")))
                ];
                $rules[$validationField . 'marks_in_percentage'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        $resultId = !empty($fields['result']) ? $fields['result'] : null;
                        return $resultId && app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::MARKS, $resultId);
                    }),
                    'nullable',
                    "numeric"
                ];
                $rules[$validationField . 'cgpa_scale'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        $resultId = !empty($fields['result']) ? $fields['result'] : null;
                        return $resultId && app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::SCALE, $resultId);
                    }),
                    'nullable',
                    Rule::in([EnrollmentEducation::GPA_OUT_OF_FOUR, EnrollmentEducation::GPA_OUT_OF_FIVE]),
                    "integer"
                ];
                $rules[$validationField . 'cgpa'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        $resultId = !empty($fields['result']) ? $fields['result'] : null;
                        return $resultId && app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::CGPA, $resultId);
                    }),
                    'nullable',
                    "max:5"
                ];
                $rules[$validationField . 'year_of_passing'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        $resultId = !empty($fields['result']) ? $fields['result'] : null;
                        return $resultId && app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::YEAR_OF_PASS, $resultId);
                    }),
                    'nullable'
                ];
                $rules[$validationField . 'expected_year_of_passing'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        $resultId = !empty($fields['result']) ? $fields['result'] : null;
                        return $resultId && app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::EXPECTED_YEAR_OF_PASS, $resultId);
                    }),
                    'nullable',
                    'string'
                ];
                $rules[$validationField . 'duration'] = [
                    "nullable",
                    "integer"
                ];
                $rules[$validationField . 'achievements'] = [
                    "nullable",
                    "string"
                ];
                $rules[$validationField . 'achievements_en'] = [
                    "nullable",
                    "string"
                ];
            }
        }
        if (!empty($data['physical_disability_status'])) {
            $rules['physical_disabilities'] = [
                Rule::requiredIf(function () use ($data) {
                    return $data['physical_disability_status'] == BaseModel::TRUE;
                }),
                'nullable',
                "array",
                "min:1"
            ];
            $rules['physical_disabilities.*'] = [
                Rule::requiredIf(function () use ($data) {
                    return $data['physical_disability_status'] == BaseModel::TRUE;
                }),
                'nullable',
                "int",
                "distinct",
                "min:1",
                "exists:physical_disabilities,id,deleted_at,NULL",
            ];
        }

        if (!empty($data['payment_info'])) {
            $rules['payment_gateway_type'] = [
                'required',
                Rule::in(array_values(PaymentTransactionHistory::PAYMENT_GATEWAYS))
            ];
        }
        return \Illuminate\Support\Facades\Validator::make($data, $rules, $customMessage);
    }

    public function buildExcelValidation(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            "course_id" => [
                'required',
                'exists:courses,id,deleted_at,NULL',
                'int',
                'min:1',
            ],
            "batch_id" => [
                'required',
                'exists:batches,id,deleted_at,NULL',
                'int',
                'min:1'
            ]
        ];
        return Validator::make($request->all(), $rules);
    }

    public function excelFileFormatValidation(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        $rules = [
            'program_id' => [
                'nullable',
                'exists:programs,id,deleted_at,NULL',
                'int'
            ],
            'course_id' => [
                'required',
                'exists:courses,id,deleted_at,NULL',
                'int',
                'min:1',
//                function ($attr, $value, $failed) use ($data) {
//                    $courseEnrollments = CourseEnrollment::where('youth_id', $data['youth_id'])->where('course_id', $value)->get();
//                    foreach ($courseEnrollments as $courseEnrollment) {
//                        if ($courseEnrollment->saga_status == BaseModel::SAGA_STATUS_CREATE_PENDING ||
//                            $courseEnrollment->saga_status == BaseModel::SAGA_STATUS_UPDATE_PENDING ||
//                            $courseEnrollment->saga_status == BaseModel::SAGA_STATUS_DESTROY_PENDING) {
//                            $failed("You already enrolled in this course but enrollment process is in Pending status");
//                        } else if ($courseEnrollment->saga_status == BaseModel::SAGA_STATUS_COMMIT) {
//                            $failed("You already enrolled in this course!");
//                        }
//                    }
//                }
            ],
            'training_center_id' => [
                'nullable',
                'exists:training_centers,id,deleted_at,NULL',
                'int',
                'min:1'
            ],
            'batch_id' => [
                'nullable',
                'exists:batches,id,deleted_at,NULL',
                'int',
                'min:1'
            ],
            "course_enrollment_excel_file" => [
                "required",
                "mimes:xlsx"
            ]
        ];
        return Validator::make($data, $rules);
    }

    private function getFilterValueResultWise(array &$educationInfo): void
    {
        foreach ($educationInfo as $educationLevelId => $value) {
            if (!empty($value[self::RESULT]) && !app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::MARKS, $value[self::RESULT])) {
                unset($educationInfo[$educationLevelId]['marks_in_percentage']);
            } elseif (!empty($value[self::RESULT]) && !app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::SCALE, $value[self::RESULT])) {
                unset($educationInfo[$educationLevelId]['cgpa_scale']);
            } elseif (!empty($value[self::RESULT]) && !app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::CGPA, $value[self::RESULT])) {
                unset($educationInfo[$educationLevelId]['cgpa']);
            } elseif (!empty($value[self::RESULT]) && !app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::YEAR_OF_PASS, $value[self::RESULT])) {
                unset($educationInfo[$educationLevelId]['year_of_passing']);
            } elseif (!empty($value[self::RESULT]) && !app(CourseEnrollmentService::class)->getRequiredStatus(EnrollmentEducation::EXPECTED_YEAR_OF_PASS, $value[self::RESULT])) {
                unset($educationInfo[$educationLevelId]['expected_year_of_passing']);
            }
        }
    }

    private function getColumnLabel(string $attribute): string
    {
        return ucfirst(str_replace('_', ' ', $attribute));
    }

    public function getSkills(int $courseId): array
    {
        return Course::findOrFail($courseId)->skills()->pluck("id")->toArray();
    }

}
