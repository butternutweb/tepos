<?php

use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status')->insert([
            'name' => 'Active',
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('status')->insert([
            'name' => 'Inactive',
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('status')->insert([
            'name' => 'Completed',
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('status')->insert([
            'name' => 'On Progress',
            'created_at' => \Carbon\Carbon::now(),
        ]);
    }
}
