<?php

use Illuminate\Database\Seeder;

class APIResetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(APIReset\GamesSeeder::class);
        $this->call(APIReset\BlogSeeder::class);
    }
}
