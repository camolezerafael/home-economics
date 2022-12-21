<?php

namespace Database\Seeders;

use App\Models\FromTo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FromToTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FromTo::factory(1)->create();
    }
}
