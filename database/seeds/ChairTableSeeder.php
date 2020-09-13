<?php

use Illuminate\Database\Seeder;
use App\Chair;

class ChairTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Chair::truncate();

        $rowCounter = 1;
        $numberCounter = 1;

        for ($i=1; $i<=15; $i++) {

            Chair::create([
                'id' => $i,
                'row' => $rowCounter,
                'number' => $numberCounter,
                'status' => 'sold',
            ]);

            if ($numberCounter % 6 === 0) {
                $rowCounter++;
                $numberCounter = 1;
            } else {
                $numberCounter++;
            }
        }

        Chair::create([
            'id' => 16,
            'row' => 3,
            'number' => 4,
            'status' => 'free',
        ]);
        Chair::create([
            'id' => 17,
            'row' => 3,
            'number' => 5,
            'status' => 'free',
        ]);
        Chair::create([
            'id' => 18,
            'row' => 3,
            'number' => 6,
            'status' => 'sold',
        ]);
    }
}
