<?php

use Illuminate\Database\Seeder;
use App\Models\General\TahunAkademik;

class TahunAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => '1','year' => '2025/2026','start_date' => '2025-03-20','end_date' => '2026-03-19','is_active' => 1,'created_at' => now(), 'updated_at' => now()],
            ['id' => '2','year' => '2026/2027','start_date' => '2026-03-20','end_date' => '2027-03-18','is_active' => 0,'created_at' => now(), 'updated_at' => now()]
       ];
       TahunAkademik::insert($records);
    }
}
