<?php

namespace App\Helpers\Classes;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class ExcelExport implements FromView
{

    public function view(): View
    {
        return view('excel.excel');
    }
}
