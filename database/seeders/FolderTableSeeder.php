<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FolderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('folder')->insert([
            ['id' => 1, 'name' => 'Brand', 'deleted_at' => null],
            ['id' => 2, 'name' => 'Category', 'deleted_at' => null],
            ['id' => 3, 'name' => 'Product', 'deleted_at' => null],
        ]);
    }
}
