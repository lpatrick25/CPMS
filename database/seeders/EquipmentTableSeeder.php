<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EquipmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Equipment Data
        $items = [
            // Equipment
            [
                'name' => 'Laptop',
                'description' => 'Dell Inspiron 15',
                'quantity' => 10,
                'remaining_quantity' => 10,
                'has_serial' => true,
                'equipment_in_charge' => 4,
                'category' => 'Equipment',
            ],
            [
                'name' => 'Projector',
                'description' => 'Epson Projector X120',
                'quantity' => 5,
                'remaining_quantity' => 5,
                'has_serial' => true,
                'equipment_in_charge' => 4,
                'category' => 'Equipment',
            ],
            [
                'name' => 'Printer',
                'description' => 'HP LaserJet Pro MFP',
                'quantity' => 3,
                'remaining_quantity' => 3,
                'has_serial' => true,
                'equipment_in_charge' => 4,
                'category' => 'Equipment',
            ],
            [
                'name' => 'Microscope',
                'description' => 'Biological Microscope 1000x',
                'quantity' => 7,
                'remaining_quantity' => 7,
                'has_serial' => true,
                'equipment_in_charge' => 4,
                'category' => 'Equipment',
            ],
            [
                'name' => 'Whiteboard',
                'description' => '120x90cm Magnetic Whiteboard',
                'quantity' => 4,
                'remaining_quantity' => 4,
                'has_serial' => false,
                'equipment_in_charge' => 4,
                'category' => 'Equipment',
            ],

            // Tools
            [
                'name' => 'Hammer',
                'description' => '16oz Claw Hammer',
                'quantity' => 5,
                'remaining_quantity' => 5,
                'has_serial' => false,
                'equipment_in_charge' => 4,
                'category' => 'Tool',
            ],
            [
                'name' => 'Screwdriver Set',
                'description' => '10-Piece Precision Screwdriver Set',
                'quantity' => 8,
                'remaining_quantity' => 8,
                'has_serial' => false,
                'equipment_in_charge' => 4,
                'category' => 'Tool',
            ],
            [
                'name' => 'Wrench',
                'description' => 'Adjustable Wrench 12-inch',
                'quantity' => 6,
                'remaining_quantity' => 6,
                'has_serial' => false,
                'equipment_in_charge' => 4,
                'category' => 'Tool',
            ],
            [
                'name' => 'Pliers',
                'description' => 'Multi-purpose Pliers with Cutter',
                'quantity' => 7,
                'remaining_quantity' => 7,
                'has_serial' => false,
                'equipment_in_charge' => 4,
                'category' => 'Tool',
            ],
            [
                'name' => 'Drill',
                'description' => 'Cordless Power Drill 18V',
                'quantity' => 3,
                'remaining_quantity' => 3,
                'has_serial' => true,
                'equipment_in_charge' => 4,
                'category' => 'Tool',
            ],
        ];

        // Insert Equipment and Generate Serial Numbers
        foreach ($items as $item) {
            $equipmentId = DB::table('equipment')->insertGetId([
                'name' => $item['name'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'remaining_quantity' => $item['remaining_quantity'],
                'has_serial' => $item['has_serial'],
                'equipment_in_charge' => $item['equipment_in_charge'],
                'category' => $item['category'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // If the equipment or tool has a serial, generate unique serials
            if ($item['has_serial']) {
                for ($i = 0; $i < $item['quantity']; $i++) {
                    DB::table('equipment_serials')->insert([
                        'equipment_id' => $equipmentId,
                        'serial_number' => strtoupper(Str::random(10)),
                        'status' => 'Available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
