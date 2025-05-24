<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch units by name
        $units = DB::table('units')->pluck('id', 'name');

        DB::table('items')->insert([
            [
                'name' => 'Whiteboard Marker',
                'description' => 'Staedtler Whiteboard Marker (Assorted Colors)',
                'unit_id' => $units['pack'],
                'quantity' => 30,
                'remaining_quantity' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bond Paper (A4)',
                'description' => 'Substance 20, A4 Bond Paper (500 sheets per ream)',
                'unit_id' => $units['ream'],
                'quantity' => 50,
                'remaining_quantity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Printer Ink (Black)',
                'description' => 'Epson 003 Black Ink Bottle',
                'unit_id' => $units['bottle'],
                'quantity' => 20,
                'remaining_quantity' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Printer Ink (Color)',
                'description' => 'Epson 003 Cyan, Magenta, Yellow Ink Bottles',
                'unit_id' => $units['bottle'],
                'quantity' => 15,
                'remaining_quantity' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Test Tubes',
                'description' => 'Glass Test Tubes with Rim (12 pcs/set)',
                'unit_id' => $units['set'],
                'quantity' => 40,
                'remaining_quantity' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lab Gloves',
                'description' => 'Disposable Nitrile Gloves (Medium Size, 100 pcs/box)',
                'unit_id' => $units['box'],
                'quantity' => 20,
                'remaining_quantity' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cables & Extension Wires',
                'description' => '10m Heavy-Duty Extension Cord with Multiple Outlets',
                'unit_id' => $units['roll'],
                'quantity' => 10,
                'remaining_quantity' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Board Eraser',
                'description' => 'Magnetic Whiteboard Eraser (Soft Felt)',
                'unit_id' => $units['piece'],
                'quantity' => 15,
                'remaining_quantity' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ethernet Cables',
                'description' => 'CAT6 Ethernet Cable (Per Meter)',
                'unit_id' => $units['meter'],
                'quantity' => 100,
                'remaining_quantity' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chalk',
                'description' => 'Dustless Chalk (White, 100 pcs/box)',
                'unit_id' => $units['box'],
                'quantity' => 30,
                'remaining_quantity' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Printer',
                'description' => 'Epson L3110 EcoTank All-in-One Printer',
                'unit_id' => $units['piece'],
                'quantity' => 5,
                'remaining_quantity' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
