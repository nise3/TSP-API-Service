<?php

namespace Database\Seeders;

use App\Models\EduBoard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EduBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        EduBoard::query()->truncate();

        $boardName = [
            array('id' => '1', 'code' => 'Barisal', 'title_en' => 'Barisal', 'title' => 'বরিশাল', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '2', 'code' => 'Chittagong', 'title_en' => 'Chittagong', 'title' => 'চট্টগ্রাম', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '3', 'code' => 'Comilla', 'title_en' => 'Comilla', 'title' => 'কুমিল্লা', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '4', 'code' => 'Dhaka', 'title_en' => 'Dhaka', 'title' => 'ঢাকা', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '5', 'code' => 'Dinajpur', 'title_en' => 'Dinajpur', 'title' => 'দিনাজপুর', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '6', 'code' => 'Jessore', 'title_en' => 'Jessore', 'title' => 'যশোর', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '7', 'code' => 'Mymensingh', 'title_en' => 'Mymensingh', 'title' => 'ময়মনসিংহ', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '8', 'code' => 'Rajshahi', 'title_en' => 'Rajshahi', 'title' => 'রাজশাহী', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '9', 'code' => 'Sylhet', 'title_en' => 'Sylhet', 'title' => 'সিলেট', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '10', 'code' => 'NU', 'title_en' => 'National University', 'title' => 'National University', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL)
        ];

        EduBoard::insert($boardName);
        Schema::enableForeignKeyConstraints();
    }
}
