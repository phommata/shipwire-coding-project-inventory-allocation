<?php

use Illuminate\Database\Seeder;

class InventoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('inventories')->insert(
            [
                'name' => 'A',
                'quantity' => 2,
            ],
            [
                'name' => 'B',
                'quantity' => 3
            ],
            [
                'name' => 'C',
                'quantity' => 1
            ],
            [
                'name' => 'D',
                'quantity' => 0
            ],
            [
                'name' => 'E',
                'quantity' => 0
            ]
        );

    }
}
