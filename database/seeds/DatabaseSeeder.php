<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tb_level')->insert([
            "id" => 1,
            "level" => "super Admin",
            "deskripsi" => "super admin - level 1",
            "created_at" => Carbon::now()->toDateTimeString()
        ]);
        DB::table('tb_level')->insert([
            "id" => 2,
            "level" => "Admin",
            "deskripsi" => "admin - level 2",
            "created_at" => Carbon::now()->toDateTimeString()
        ]);
        DB::table('tb_level')->insert([
            "id" => 3,
            "level" => "Member",
            "deskripsi" => "member - level 3",
            "created_at" => Carbon::now()->toDateTimeString()
        ]);
        DB::table('users')->insert([
            "id" => 1,
            "name" => "ricoo",
            "email" => "ricoo@ringkas.ai",
            "email_verified_at" => Carbon::now()->toDateTimeString(),
            "password" => Hash::make("admin"),
            "id_level" => 1,
            "created_at" => Carbon::now()->toDateTimeString()
        ]);
    }
}
