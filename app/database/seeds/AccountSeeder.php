<?php

use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminID = DB::table('admin')->insertGetId([
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('account')->insert([
            'username' => strtolower('admin'),
            'password' => bcrypt('password'),
            'email' => strtolower('admin@admin.com'),
            'name' => 'Administrator',
            'phone' => '089908990899',
            'child_id' => $adminID,
            'child_type' => 'Admin',
            'status_id' => \App\Status::where('name', 'Active')->first()->id,
            'created_at' => \Carbon\Carbon::now(),
        ]);

        $ownerID = DB::table('owner')->insertGetId([
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('account')->insert([
            'username' => strtolower('owner'),
            'password' => bcrypt('password'),
            'email' => strtolower('owner@owner.com'),
            'name' => 'Owner',
            'phone' => '089908990899',
            'child_id' => $ownerID,
            'child_type' => 'Owner',
            'status_id' => \App\Status::where('name', 'Active')->first()->id,
            'created_at' => \Carbon\Carbon::now(),
        ]);

        $storeID = DB::table('store')->insertGetId([
            'name' => 'Store A',
            'owner_id' => $ownerID,
        ]);

        $staff1ID = DB::table('staff')->insertGetId([
            'store_id' => $storeID,
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('account')->insert([
            'username' => strtolower('owner_staff1'),
            'password' => bcrypt('password'),
            'email' => strtolower('staff1@staff.com'),
            'name' => 'Staff 1',
            'phone' => '089908990899',
            'child_id' => $staff1ID,
            'child_type' => 'Staff',
            'status_id' => \App\Status::where('name', 'Active')->first()->id,
            'created_at' => \Carbon\Carbon::now(),
        ]);

        $staff2ID = DB::table('staff')->insertGetId([
            'store_id' => $storeID,
            'created_at' => \Carbon\Carbon::now(),
        ]);
    
        DB::table('account')->insert([
            'username' => strtolower('owner_staff2'),
            'password' => bcrypt('password'),
            'email' => strtolower('staff2@staff.com'),
            'name' => 'Staff 2',
            'phone' => '089908990899',
            'child_id' => $staff2ID,
            'child_type' => 'Staff',
            'status_id' => \App\Status::where('name', 'Active')->first()->id,
            'created_at' => \Carbon\Carbon::now(),
        ]);
    }
}
