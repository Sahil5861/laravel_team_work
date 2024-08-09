<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\Country;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = Country::all();

        foreach ($countries as $country) {
            for ($i = 1; $i <= 5; $i++) {
                State::create(['name' => "State $i of {$country->name}", 'country_id' => $country->id]);
            }
        }        
    }
}
