<?php

namespace Database\Seeders;

use App\Models\EduBoard;
use Illuminate\Database\Seeder;

class EduBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $boardName = [
            [
                "title_en" => "Barisal",
                "title" => "বরিশাল"
            ],
            [
                "title_en" => "Chittagong",
                "title" => "চট্টগ্রাম"
            ],
            [
                "title_en" => "Comilla",
                "title" => "কুমিল্লা"
            ],
            [
                "title_en" => "Dhaka",
                "title" => "ঢাকা"
            ],
            [
                "title_en" => "Dinajpur",
                "title" => "দিনাজপুর"
            ],
            [
                "title_en" => "Jessore",
                "title" => "যশোর"
            ],
            [
                "title_en" => "Mymensingh",
                "title" => "ময়মনসিংহ"
            ],
            [
                "title_en" => "Rajshahi",
                "title" => "রাজশাহী"
            ],
            [
                "title_en" => "Sylhet",
                "title" => "সিলেট"
            ],
            [
                "title_en" => "National University",
                "title" => "National University"
            ],
        ];

        EduBoard::insert($boardName);
    }
}
