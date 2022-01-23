<?php

namespace Database\Seeders;

use App\Models\EduGroup;
use App\Models\PhysicalDisability;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PhysicalDisabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        PhysicalDisability::query()->truncate();

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

        Schema::enableForeignKeyConstraints();
    }
}
