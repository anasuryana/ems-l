<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['admin', 'editor', 'user'];

        foreach ($roles as $role) {
            if (DB::table('roles')->where(['name' => $role])->count() == 0) {
                DB::table('roles')->insert(['name' => $role]);
            }
        }
    }
}
