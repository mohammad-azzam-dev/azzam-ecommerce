<?php

use App\Model\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = Admin::create([
            'id' => 1,
            'f_name' => 'Super',
            'l_name' => 'Admin',
            'phone' => '01759412381',
            'email' => 'super.admin@email.com',
            'image' => 'def.png',
            'password' => bcrypt(12345678),
            'remember_token' =>Str::random(10),
            'created_at'=>now(),
            'updated_at'=>now()
        ]);

        $superAdmin->assignRole('super-admin');
    }
}
