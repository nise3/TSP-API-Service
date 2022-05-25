<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GeoLocationDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(LocDivisionsTableSeeder::class);
        $this->call(LocDistrictsTableSeeder::class);
        $this->call(LocUpazilasTableSeeder::class);
        $this->call(LocCityCorporationsTableSeeder::class);
        $this->call(LocCityCorporationWardsTableSeeder::class);
        $this->call(LocUnionsMunicipalityCityAreaSeeder::class);
        $this->call(LocUnionsTableSeeder::class);
    }
}
