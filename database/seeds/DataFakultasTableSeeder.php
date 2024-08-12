<?php

use Illuminate\Database\Seeder;
use App\Models\General\DataFakultas;

class DataFakultasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => '1','nama_fakultas' => 'Bisnis','kode_fakultas' => 'FEB','created_at' => now(), 'updated_at' => now()],
            ['id' => '2','nama_fakultas' => 'Komputer','kode_fakultas' => 'FKOM','created_at' => now(), 'updated_at' => now()],
            ['id' => '3','nama_fakultas' => 'Seni','kode_fakultas' => 'FS','created_at' => now(), 'updated_at' => now()],
            ['id' => '4','nama_fakultas' => 'Teknik','kode_fakultas' => 'FT','created_at' => now(), 'updated_at' => now()],
            ['id' => '5','nama_fakultas' => 'Pendidikan, Bahasa dan Budaya','kode_fakultas' => 'FPBB','created_at' => now(), 'updated_at' => now()]
       ];
       DataFakultas::insert($records);
    }
}
