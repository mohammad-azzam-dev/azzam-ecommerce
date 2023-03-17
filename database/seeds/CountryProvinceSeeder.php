<?php

use Illuminate\Database\Seeder;

class CountryProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('country_provinces')->truncate();
        \Illuminate\Support\Facades\DB::table('country_provinces')->insert([
           [
               'country_code' => 'KW',
               'province' => 'الكويت',
           ],
           [
               'country_code' => 'KW',
               'province' => 'حولي',
           ],
           [
               'country_code' => 'KW',
               'province' => 'مبارك الكبير',
           ],
           [
               'country_code' => 'KW',
               'province' => 'الجهراء',
           ],
           [
               'country_code' => 'KW',
               'province' => 'الأحمدي',
           ],
        ]);
    }
}
