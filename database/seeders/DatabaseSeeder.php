<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RoleSeeder::class
        ]);

        User::factory()->create([
            'name' => 'Bang Admin',
            'email' => 'test@example.com',
            'password' => Hash::make('tampan'),
            'role_id' => DB::table('roles')->where('name', 'admin')->first('id')->id,
            'nick_name' => 'bang'
        ]);
    }
}
