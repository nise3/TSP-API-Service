<?php

namespace Database\Seeders;

use App\Models\PhysicalDisability;
use Illuminate\Database\Seeder;

class PhysicalDisabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PhysicalDisability::insert([
            [
                "title" => "Visual Disabilities",
                "title_en" => "Visual Disabilities",
            ],
            [
                "title" => "Hearing Disabilities",
                "title_en" => "Hearing Disabilities",
            ],
            [
                "title" => "Mental Health Disabilities",
                "title_en" => "Mental Health Disabilities",
            ],
            [
                "title" => "Intellectual Disabilities",
                "title_en" => "Intellectual Disabilities",
            ],
            [
                "title" => "Social Disabilities",
                "title_en" => "Social Disabilities",
            ]
        ]);
    }
}
