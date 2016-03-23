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
        $this->call(APIReset\BlogsSeeder::class);
        $this->call(APIReset\CommentsSeeder::class);
    }
}
