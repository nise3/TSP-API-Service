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
            array('id' => '1', 'code' => 'Visual_Dis', 'title' => 'চাক্ষুষ অক্ষমতা', 'title_en' => 'Visual Disabilities', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '2', 'code' => 'Hearing_Dis', 'title' => 'শ্রবণ প্রতিবন্ধী', 'title_en' => 'Hearing Disabilities', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '3', 'code' => 'Mental_H_Dis', 'title' => 'মানসিক স্বাস্থ্য অক্ষমতা', 'title_en' => 'Mental Health Disabilities', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '4', 'code' => 'Intellectual_Dis', 'title' => 'বুদ্ধিবৃত্তিক অক্ষমতা', 'title_en' => 'Intellectual Disabilities', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '5', 'code' => 'Social_Dis', 'title' => 'সামাজিক প্রতিবন্ধী', 'title_en' => 'Social Disabilities', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL)
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
