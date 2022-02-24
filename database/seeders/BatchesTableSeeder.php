<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BatchesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();

        DB::table('batches')->truncate();

        DB::table('batches')->insert(array (
            array('id' => '1','code' => 'SSP00000002C0000001BT0000001','title' => 'ব্লক বাটিক - 01','title_en' => NULL,'course_id' => '1','institute_id' => '2','industry_association_id' => NULL,'branch_id' => NULL,'training_center_id' => '133','number_of_seats' => '30','registration_start_date' => '2022-02-09','registration_end_date' => '2022-02-10','batch_start_date' => '2022-02-11','batch_end_date' => '2022-02-12','available_seats' => '30','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-02-09 23:32:48','updated_at' => '2022-02-09 23:32:48','deleted_at' => NULL),
            array('id' => '2','code' => 'SSP00000002C0000002BT0000001','title' => 'ফল চাষ- 01','title_en' => NULL,'course_id' => '2','institute_id' => '2','industry_association_id' => NULL,'branch_id' => NULL,'training_center_id' => '128','number_of_seats' => '20','registration_start_date' => '2022-02-09','registration_end_date' => '2022-02-10','batch_start_date' => '2022-02-11','batch_end_date' => '2022-02-16','available_seats' => '20','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-02-09 23:37:51','updated_at' => '2022-02-09 23:37:51','deleted_at' => NULL),
            array('id' => '3','code' => 'SSP00000002C0000003BT0000001','title' => 'দর্জি শিল্প- 02','title_en' => NULL,'course_id' => '3','institute_id' => '2','industry_association_id' => NULL,'branch_id' => NULL,'training_center_id' => '128','number_of_seats' => '15','registration_start_date' => '2022-02-09','registration_end_date' => '2022-02-12','batch_start_date' => '2022-02-13','batch_end_date' => '2022-02-24','available_seats' => '15','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-02-09 23:39:53','updated_at' => '2022-02-09 23:39:53','deleted_at' => NULL),
            array('id' => '4','code' => 'C000000000000000001BT0000001','title' => 'Batch-01','title_en' => NULL,'course_id' => '4','institute_id' => '1','industry_association_id' => NULL,'branch_id' => NULL,'training_center_id' => '10','number_of_seats' => '30','registration_start_date' => '2022-02-10','registration_end_date' => '2022-02-15','batch_start_date' => '2022-02-16','batch_end_date' => '2022-02-17','available_seats' => '28','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-02-10 22:05:48','updated_at' => '2022-02-15 15:47:13','deleted_at' => NULL),
            array('id' => '5','code' => 'SSP00000001C0000002BT0000001','title' => 'Batch-02','title_en' => NULL,'course_id' => '5','institute_id' => '1','industry_association_id' => NULL,'branch_id' => NULL,'training_center_id' => '3','number_of_seats' => '15','registration_start_date' => '2022-02-10','registration_end_date' => '2022-02-15','batch_start_date' => '2022-02-16','batch_end_date' => '2022-02-20','available_seats' => '13','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-02-10 22:06:32','updated_at' => '2022-02-13 00:47:16','deleted_at' => NULL),
            array('id' => '6','code' => 'SSP00000001C0000003BT0000001','title' => 'Batch-03','title_en' => NULL,'course_id' => '6','institute_id' => '1','industry_association_id' => NULL,'branch_id' => NULL,'training_center_id' => '2','number_of_seats' => '30','registration_start_date' => '2022-02-10','registration_end_date' => '2022-02-11','batch_start_date' => '2022-02-13','batch_end_date' => '2022-02-20','available_seats' => '30','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-02-10 22:07:05','updated_at' => '2022-02-10 22:07:05','deleted_at' => NULL),
            array('id' => '7','code' => 'SSP00000002C0000002BT0000001','title' => 'Interview preparation batch-1','title_en' => NULL,'course_id' => '7','institute_id' => '1','industry_association_id' => NULL,'branch_id' => NULL,'training_center_id' => '1','number_of_seats' => '12','registration_start_date' => '2022-02-14','registration_end_date' => '2022-02-20','batch_start_date' => '2022-02-28','batch_end_date' => '2022-03-31','available_seats' => '12','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-02-14 16:53:24','updated_at' => '2022-02-14 16:53:24','deleted_at' => NULL),
            array('id' => '8','code' => 'SSP00000002C0000003BT0000001','title' => 'React Batch-1','title_en' => 'React Batch-1','course_id' => '8','institute_id' => '1','industry_association_id' => NULL,'branch_id' => NULL,'training_center_id' => '127','number_of_seats' => '20','registration_start_date' => '2022-02-14','registration_end_date' => '2022-02-21','batch_start_date' => '2022-02-28','batch_end_date' => '2022-03-31','available_seats' => '20','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-02-14 17:08:13','updated_at' => '2022-02-14 17:08:13','deleted_at' => NULL),
            array('id' => '9','code' => 'SSP00000036C0000001BT0000001','title' => 'new batch','title_en' => NULL,'course_id' => '9','institute_id' => '1','industry_association_id' => NULL,'branch_id' => '4','training_center_id' => '120','number_of_seats' => '20','registration_start_date' => '2022-02-01','registration_end_date' => '2022-02-03','batch_start_date' => '2022-02-07','batch_end_date' => '2022-02-16','available_seats' => '20','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-02-14 22:32:08','updated_at' => '2022-02-14 22:32:31','deleted_at' => NULL),
            array('id' => '10','code' => 'SSP00000001C0000005BT0000001','title' => 'Test Batch Edited','title_en' => NULL,'course_id' => '10','institute_id' => '2','industry_association_id' => NULL,'branch_id' => NULL,'training_center_id' => '133','number_of_seats' => '345','registration_start_date' => '2022-02-15','registration_end_date' => '2022-02-18','batch_start_date' => '2022-01-31','batch_end_date' => '2022-02-02','available_seats' => '345','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-02-15 15:42:33','updated_at' => '2022-02-15 15:51:02','deleted_at' => '2022-02-15 15:51:02'),
            array('id' => '11','code' => 'C000000000000000001BT0000002','title' => 'Test','title_en' => NULL,'course_id' => '11','institute_id' => '2','industry_association_id' => NULL,'branch_id' => NULL,'training_center_id' => '133','number_of_seats' => '78','registration_start_date' => '2022-02-15','registration_end_date' => '2022-02-16','batch_start_date' => '2022-02-15','batch_end_date' => '2022-02-16','available_seats' => '78','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-02-15 17:03:46','updated_at' => '2022-02-15 17:03:46','deleted_at' => NULL),
            array('id' => '12','code' => 'SSP00000002C0000004BT0000001','title' => 'Computer Training Batch 1','title_en' => 'Computer Training Batch 1','course_id' => '12','institute_id' => '2','industry_association_id' => NULL,'branch_id' => NULL,'training_center_id' => '128','number_of_seats' => '20','registration_start_date' => '2022-02-15','registration_end_date' => '2022-02-28','batch_start_date' => '2022-02-16','batch_end_date' => '2022-02-28','available_seats' => '20','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-02-15 23:19:54','updated_at' => '2022-02-15 23:19:54','deleted_at' => NULL)
        ));

        Schema::enableForeignKeyConstraints();


    }
}
