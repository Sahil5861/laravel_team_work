<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class ContactPersonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ContactPerson')->insert([
            [
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
                'phone' => '1234567890',
                'designation' => 'Manager',
                'is_primary' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'janesmith@example.com',
                'phone' => '0987654321',
                'designation' => 'Assistant Manager',
                'is_primary' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Robert Johnson',
                'email' => 'robertjohnson@example.com',
                'phone' => '1122334455',
                'designation' => 'Sales Representative',
                'is_primary' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emilydavis@example.com',
                'phone' => '2233445566',
                'designation' => 'Technical Support',
                'is_primary' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Michael Wilson',
                'email' => 'michaelwilson@example.com',
                'phone' => '3344556677',
                'designation' => 'Product Manager',
                'is_primary' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jessica Lee',
                'email' => 'jessicalee@example.com',
                'phone' => '4455667788',
                'designation' => 'Marketing Manager',
                'is_primary' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'David Brown',
                'email' => 'davidbrown@example.com',
                'phone' => '5566778899',
                'designation' => 'HR Specialist',
                'is_primary' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laura Moore',
                'email' => 'lauramoore@example.com',
                'phone' => '6677889900',
                'designation' => 'Finance Manager',
                'is_primary' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kevin Taylor',
                'email' => 'kevintaylor@example.com',
                'phone' => '7788990011',
                'designation' => 'Operations Manager',
                'is_primary' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sophia Martinez',
                'email' => 'sophiamartinez@example.com',
                'phone' => '8899001122',
                'designation' => 'Customer Service Representative',
                'is_primary' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
