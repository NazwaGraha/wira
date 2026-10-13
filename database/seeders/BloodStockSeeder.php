<?php

namespace Database\Seeders;

use App\Models\BloodStock;
use Illuminate\Database\Seeder;

class BloodStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bloodTypes = ['A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-'];

        foreach ($bloodTypes as $type) {
            BloodStock::updateOrCreate(
                ['blood_type' => $type],
                [
                    'status' => 'aman',
                    'bags_count' => 25,
                    'notes' => '',
                ]
            );
        }
    }
}
