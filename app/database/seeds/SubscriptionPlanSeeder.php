<?php

use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subs_plan')->insert([
            'name' => 'Package A',
            'store_number' => '1',
            'duration_day' => '30',
            'price' => 75000,
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('subs_plan')->insert([
            'name' => 'Package B',
            'store_number' => '2',
            'duration_day' => '30',
            'price' => 100000,
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('subs_plan')->insert([
            'name' => 'Package C',
            'store_number' => '3',
            'duration_day' => '30',
            'price' => 150000,
            'created_at' => \Carbon\Carbon::now(),
        ]);
    }
}
