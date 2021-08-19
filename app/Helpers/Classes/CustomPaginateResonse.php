<?php

namespace App\Helpers\Classes;

use Illuminate\Contracts\Pagination\Paginator;

class CustomPaginateResonse
{
    public static function getPaginateData(Paginator $paginator):array
    {
        $paginateData=(object)$paginator->toArray();
        return [
            "current_page" => $paginateData->current_page,
            "page_size" => $paginateData->last_page,
            "limit" => $paginateData->per_page,
            "total" => $paginateData->total,
        ];
    }
}
