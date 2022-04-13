<?php

namespace App\Services;

use App\Helpers\Classes\ExcelExport;
use App\Models\BaseModel;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\EduBoard;
use App\Models\EducationLevel;
use App\Models\EduGroup;
use App\Models\ExamDegree;
use App\Models\ExamType;
use App\Models\LocDistrict;
use App\Models\LocDivision;
use App\Models\LocUpazila;
use App\Models\PhysicalDisability;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\NoReturn;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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
            'attribute' => 'freedom_fighter',
            'label' => 'Freedom Fighter',
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
            "attribute" => 'is_currently_employed',
            "label" => "Is Currently Employed",
            'column' => ''
        ],
        [
            "attribute" => 'years_of_experiences',
            "label" => "Year Of Experiences",
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
            "label" => "Result Type",
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
            "label" => "Result Type",
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
            "label" => "Result Type",
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
            "label" => "Result Type",
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
            "label" => "Result Type",
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

    /**
     * @throws Exception
     */
    public function buildImportExcel(int $courseId)
    {
        $objPHPExcel = new Spreadsheet();
        foreach (self::YOUTH_PROFILE_BASIC_FIELDS as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($value['column'], $value['label']);
            $objPHPExcel->getActiveSheet()->getColumnDimension(substr_replace($value['column'], "", -1))->setWidth(strlen($value['label']) + 10);
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
        $trueFalse = [
            BaseModel::FALSE => "True",
            BaseModel::TRUE => "False"
        ];
        $trueFalseDropDown = "";
        foreach ($trueFalse as $key => $value) {
            $trueFalseDropDown .= $key . " | " . $value;
        }

        $physicalDisabilityDropDown = "";
        foreach (PhysicalDisability::all() as $value) {
            $physicalDisabilityDropDown .= $value->id . " | " . $value->title;
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
        $this->dropDownColumnBuilder($objPHPExcel, self::PHYSICAL_DISABILITY_STATUS_COLUMN, $trueFalseDropDown);
        $this->dropDownColumnBuilder($objPHPExcel, self::PHYSICAL_DISABILITIES, $physicalDisabilityDropDown);
        $this->dropDownColumnBuilder($objPHPExcel, self::ETHNIC_GROUP_COLUMN, $trueFalseDropDown);
        $this->dropDownColumnBuilder($objPHPExcel, self::IDENTITY_NUMBER_TYPE_COLUMN, $identityNumberTypeDropdown);


        $this->dropDownColumnBuilderWorkSheetBased($objPHPExcel, self::DIVISION_COLUMN, '$A:$A', 1);
        $this->dropDownColumnBuilderWorkSheetBased($objPHPExcel, self::DISTRICT_COLUMN, '$B:$B', 2);
        $this->dropDownColumnBuilderWorkSheetBased($objPHPExcel, self::UPAZILA_COLUMN, '$C:$C', 3);


        $fileName = 'course-enrollment.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$fileName");
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $writer = new Xlsx($objPHPExcel);
        $writer->save('php://output');

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
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column, ucfirst($columnPrefix) . " " . $value['label']);
                $objPHPExcel->getActiveSheet()->getColumnDimension(substr_replace($column, "", -1))->setWidth(strlen($value['label']) + 10);
                $this->dropdownFormula($objPHPExcel, $value, $column, $columnPrefix);
                $value['attribute'] = strtolower($columnPrefix) . "_" . $value['attribute'];
                $value['label'] = ucfirst($columnPrefix) . " " . $value['label'];
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
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column, $value['label']);
                    $objPHPExcel->getActiveSheet()->getColumnDimension(substr_replace($column, "", -1))->setWidth(strlen($value['label']) + 10);
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
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column, $value['label']);
                    $objPHPExcel->getActiveSheet()->getColumnDimension(substr_replace($column, "", -1))->setWidth(strlen($value['label']) + 10);
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
        $attribute = $value['attribute'];
        if ($value['attribute'] == self::EXAM_DEGREE_ID) {
            $attribute = $columnPrefix . "_" . $value['attribute'];
        }
        if (!empty($dropDownValues[$attribute])) {
            $dropDownData = $dropDownValues[$attribute];
            $this->dropDownColumnBuilder($objPHPExcel, $column, $dropDownData);
        }

    }

    private function getEducationBoard(): string
    {
        $eduBoard = "";
        foreach (EduBoard::all() as $value) {
            $eduBoard .= $value->id . " | " . $value->title . ",";
        }
        return $eduBoard;
    }

    private function getEduGroup(): string
    {
        $eduGroup = "";
        foreach (EduGroup::all() as $value) {
            $eduGroup .= $value->id . " | " . $value->title . ",";;
        }
        return $eduGroup;
    }

    private function getResultType(): string
    {
        $eduGroup = "";
        foreach (config("nise3.exam_degree_results") as $value) {
            $eduGroup .= $value['id'] . " | " . $value['title'] . ",";
        }
        return $eduGroup;
    }

    private function getPassingYear(): string
    {
        $passingYear = "";
        $startingYear = 1972;
        $endingYear = (int)date("Y");
        for ($i = $endingYear; $i >= $startingYear; $i--) {
            $passingYear .= $i . ",";
        }
        return $passingYear;
    }

    #[ArrayShape(["psc_exam_degree_id" => "string", "jsc_exam_degree_id" => "string", "ssc_exam_degree_id" => "string", "hsc_exam_degree_id" => "string", "diploma_exam_degree_id" => "string", "honours_exam_degree_id" => "string", "masters_exam_degree_id" => "string"])]
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
            "honours_exam_degree_id" => $educationDegreeGraduate,
            "masters_exam_degree_id" => $educationDegreeMasters
        ];
    }

}
