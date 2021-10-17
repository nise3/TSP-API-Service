<?php

namespace Database\Seeders;

use App\Models\EduGroup;
use Illuminate\Database\Seeder;

class EduGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = [
            [
                "title_en" => "Science",
                'code' => 'Science',
                "title" => "বিজ্ঞান"

            ],
            [
                "title_en" => "Arts and Humanities",
                'code' => 'Humanities',
                "title" => "মানবিক"

            ],
            [
                "title_en" => "Commerce or Business Studies",
                'code' => 'Commerce',
                "title" => "ব্যবসায় শিক্ষা"
            ]
        ];

        EduGroup::insert($groups);
    }
}
