<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DealersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dealers')->insert([
            [
                'business_name' => 'Alpha Tech Solutions',
                'business_email' => 'alpha@tech.com',
                'phone_number' => '1234567890',
                'contact_person_id' => 1,
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'authenticated' => true,
                'GST_number' => Str::random(15),
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Beta Innovations',
                'business_email' => 'beta@innovations.com',
                'phone_number' => '0987654321',
                'contact_person_id' => 2,
                'city' => 'Los Angeles',
                'state' => 'CA',
                'country' => 'USA',
                'authenticated' => true,
                'GST_number' => Str::random(15),
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Delta Corp',
                'business_email' => 'delta@corp.com',
                'phone_number' => '2233445566',
                'contact_person_id' => 3,
                'city' => 'Houston',
                'state' => 'TX',
                'country' => 'USA',
                'authenticated' => true,
                'GST_number' => Str::random(15),
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Zeta Industries',
                'business_email' => 'zeta@industries.com',
                'phone_number' => '4455667788',
                'contact_person_id' => 4,
                'city' => 'Philadelphia',
                'state' => 'PA',
                'country' => 'USA',
                'authenticated' => true,
                'GST_number' => Str::random(15),
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Eta Solutions',
                'business_email' => 'eta@solutions.com',
                'phone_number' => '5566778899',
                'contact_person_id' => 5,
                'city' => 'San Antonio',
                'state' => 'TX',
                'country' => 'USA',
                'authenticated' => false,
                'GST_number' => null,
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Iota Dynamics',
                'business_email' => 'iota@dynamics.com',
                'phone_number' => '7788990011',
                'contact_person_id' => 6,
                'city' => 'Dallas',
                'state' => 'TX',
                'country' => 'USA',
                'authenticated' => true,
                'GST_number' => Str::random(15),
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Kappa Enterprises',
                'business_email' => 'kappa@enterprises.com',
                'phone_number' => '8899001122',
                'contact_person_id' => 7,
                'city' => 'San Jose',
                'state' => 'CA',
                'country' => 'USA',
                'authenticated' => false,
                'GST_number' => null,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
