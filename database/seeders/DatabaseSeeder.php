<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\Request;
use App\Models\ItemRequest;
use App\Models\Notification;
use App\Models\Unit;
use App\Models\Item;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create Units
        $units = [
            ['name' => 'Piece', 'description' => 'Individual unit'],
            ['name' => 'Box', 'description' => 'Box containing multiple items'],
            ['name' => 'Set', 'description' => 'A collection of items'],
        ];

        foreach ($units as $unitData) {
            Unit::create($unitData);
        }

        // Create Users with different roles
        $users = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'custodian@example.com',
                'role' => 'Custodian',
                'password' => Hash::make('password'),
                'contact_no' => '09123456789',
                'department' => 'Maintenance',
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'president@example.com',
                'role' => 'President',
                'password' => Hash::make('password'),
                'contact_no' => '09123456788',
                'department' => 'Administration',
            ],
            [
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'email' => 'facilities@example.com',
                'role' => 'Facilities In-charge',
                'password' => Hash::make('password'),
                'contact_no' => '09123456787',
                'department' => 'Facilities',
            ],
            [
                'first_name' => 'Bob',
                'last_name' => 'Brown',
                'email' => 'employee1@example.com',
                'role' => 'Employee',
                'password' => Hash::make('password'),
                'contact_no' => '09123456786',
                'department' => 'IT',
            ],
            [
                'first_name' => 'Carol',
                'last_name' => 'Davis',
                'email' => 'employee2@example.com',
                'role' => 'Employee',
                'password' => Hash::make('password'),
                'contact_no' => '09123456785',
                'department' => 'HR',
            ],
            [
                'first_name' => 'Admin',
                'last_name' => 'System',
                'email' => 'admin@example.com',
                'role' => 'System Admin',
                'password' => Hash::make('password'),
                'contact_no' => '09123456784',
                'department' => 'IT',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Create Facilities
        $facilities = [
            ['name' => 'Com Lab 1', 'description' => 'Computer Laboratory 1', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Com Lab 2', 'description' => 'Computer Laboratory 2', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Room 101', 'description' => 'Classroom 101', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Room 102', 'description' => 'Classroom 102', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Room 103', 'description' => 'Classroom 103', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Room 104', 'description' => 'Classroom 104', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Room 105', 'description' => 'Classroom 105', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Room 106', 'description' => 'Classroom 106', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Room 107', 'description' => 'Classroom 107', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Room 108', 'description' => 'Classroom 108', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Room 109', 'description' => 'Classroom 109', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Room 110', 'description' => 'Classroom 110', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Sci-Lab 1', 'description' => 'Science Laboratory 1', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Sci-Lab 2', 'description' => 'Science Laboratory 2', 'facility_status' => 'Under Maintenance', 'facility_in_charge' => 3],
            ['name' => 'Business Incubation Facility', 'description' => 'Facility for business startups', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Gymnasium', 'description' => 'Indoor sports facility', 'facility_status' => 'Available', 'facility_in_charge' => 3],
            ['name' => 'Evacuation Center', 'description' => 'Emergency evacuation facility', 'facility_status' => 'Available', 'facility_in_charge' => 3],
        ];

        foreach ($facilities as $facilityData) {
            Facility::create([
                'facility_name' => $facilityData['name'],
                'facility_description' => $facilityData['description'],
                'facility_status' => $facilityData['facility_status'],
                'facility_in_charge' => $facilityData['facility_in_charge'],
            ]);
        }

        // Create Items
        $items = [
            ['name' => 'Laptop', 'description' => 'Dell XPS 13', 'unit_id' => 1, 'quantity' => 50, 'remaining_quantity' => 50],
            ['name' => 'Projector', 'description' => 'Epson 1080p', 'unit_id' => 1, 'quantity' => 10, 'remaining_quantity' => 10],
            ['name' => 'Microscope', 'description' => 'Lab-grade microscope', 'unit_id' => 1, 'quantity' => 20, 'remaining_quantity' => 20],
            ['name' => 'Chairs', 'description' => 'Stackable chairs', 'unit_id' => 3, 'quantity' => 100, 'remaining_quantity' => 100],
        ];

        foreach ($items as $itemData) {
            Item::create($itemData);
        }

        // Create Facility Reservations (by Employees only)
        for ($i = 0; $i < 5; $i++) {
            FacilityReservation::create([
                'employee_id' => $faker->randomElement([4, 5]), // Only Employee role users (Bob or Carol)
                'facility_id' => $faker->numberBetween(1, 17),
                'reservation_date' => $faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
                'start_time' => $faker->time('H:i:s', '12:00:00'),
                'end_time' => $faker->time('H:i:s', '18:00:00'),
                'purpose' => $faker->sentence,
                'status' => $faker->randomElement(['Pending', 'Confirmed', 'Approved', 'Denied']),
                'approved_by' => $faker->randomElement([1, 2, 3, null]),
            ]);
        }

        // Create Requests and Item Requests (by Employees only)
        for ($i = 0; $i < 5; $i++) {
            $request = Request::create([
                'transaction_number' => 'TRX-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'employee_id' => $faker->randomElement([4, 5]), // Only Employee role users
                'date_requested' => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                'status' => $faker->randomElement(['Custodian Approval', 'President Approval', 'Approved', 'Rejected', 'Released']),
            ]);

            ItemRequest::create([
                'request_id' => $request->id,
                'employee_id' => $request->employee_id,
                'item_id' => $faker->numberBetween(1, 4),
                'quantity' => $faker->numberBetween(1, 5),
                'release_quantity' => $request->status === 'Released' ? $faker->numberBetween(1, 5) : null,
                'date_requested' => $request->date_requested,
                'status' => $request->status,
                'approved_by_custodian' => $request->status !== 'Custodian Approval' ? 1 : null,
                'approved_by_president' => in_array($request->status, ['Approved', 'Released']) ? 2 : null,
                'released_by_custodian' => $request->status === 'Released' ? 1 : null,
                'released_at' => $request->status === 'Released' ? now() : null,
            ]);
        }

        // Create Notifications
        for ($i = 0; $i < 10; $i++) {
            Notification::create([
                'user_id' => $faker->numberBetween(1, 6),
                'sender_id' => $faker->randomElement([1, 2, 3, null]),
                'type' => $faker->randomElement(['item_request', 'system_update']),
                'title' => $faker->sentence(4),
                'message' => $faker->paragraph,
                'reference_number' => 'TRX-' . str_pad($faker->numberBetween(1, 5), 6, '0', STR_PAD_LEFT),
                'data' => json_encode(['extra_info' => $faker->word]),
                'is_read' => $faker->boolean,
            ]);
        }
    }
}
