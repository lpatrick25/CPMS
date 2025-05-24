<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Admin',
            'middle_name' => 'System',
            'last_name' => 'Admin',
            'extension_name' => null,
            'contact_no' => '1234567890', // Make sure to change this to a unique number
            'email' => 'admin@example.com', // Replace with an email that will be used
            'password' => Hash::make('adminpassword'), // Make sure to hash the password
            'role' => 'System Admin',
            'allow_login' => 1,
            'department' => null,
        ]);
    }
}
