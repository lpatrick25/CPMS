<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsTableSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'pack', 'description' => 'Pack of items'],
            ['name' => 'ream', 'description' => 'Ream of paper'],
            ['name' => 'bottle', 'description' => 'Bottle of liquid'],
            ['name' => 'set', 'description' => 'Set of items'],
            ['name' => 'box', 'description' => 'Box of items'],
            ['name' => 'roll', 'description' => 'Roll of wire or material'],
            ['name' => 'piece', 'description' => 'Single piece'],
            ['name' => 'meter', 'description' => 'Meter measurement'],
        ];

        DB::table('units')->insert($units);
    }
}
