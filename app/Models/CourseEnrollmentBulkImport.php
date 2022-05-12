<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CourseEnrollmentBulkImport implements ToCollection, SkipsEmptyRows, WithValidation, WithHeadingRow
{

    public function prepareForValidation($data, $index): mixed
    {
        $request = request()->all();
        return $data;
    }

    public function collection(Collection $collection)
    {
        // TODO: Implement collection() method.
    }

    public function rules(): array
    {
        // TODO: Implement rules() method.
    }
}
