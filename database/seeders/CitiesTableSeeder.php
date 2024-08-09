<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\State;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states = State::all();

        foreach ($states as $state) {
            for ($i = 1; $i <= 5; $i++) {
                City::create(['name' => "City $i of {$state->name}", 'state_id' => $state->id, 'country_id' => $state->country_id]);
            }
        }        
    }
}
