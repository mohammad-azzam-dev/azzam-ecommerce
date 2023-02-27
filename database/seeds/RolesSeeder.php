<?php

use App\Model\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'super-admin',
            'guard_name' => 'admin'
        ]);
        Role::create([
            'name' => 'admin',
            'guard_name' => 'admin'
        ]);
    }
}
