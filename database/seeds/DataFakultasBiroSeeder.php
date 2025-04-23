<?php

use Illuminate\Database\Seeder;
use App\Models\General\DataFakultasBiro;

class DataFakultasBiroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => '1','nama_fakultas_biro' => 'Bisnis','kode_fakultas_biro' => 'FEB','created_at' => now(), 'updated_at' => now()],
            ['id' => '2','nama_fakultas_biro' => 'Komputer','kode_fakultas_biro' => 'FKOM','created_at' => now(), 'updated_at' => now()],
            ['id' => '3','nama_fakultas_biro' => 'Seni','kode_fakultas_biro' => 'FS','created_at' => now(), 'updated_at' => now()],
            ['id' => '4','nama_fakultas_biro' => 'Teknik','kode_fakultas_biro' => 'FT','created_at' => now(), 'updated_at' => now()],
            ['id' => '5','nama_fakultas_biro' => 'Pendidikan, Bahasa dan Budaya','kode_fakultas_biro' => 'FPBB','created_at' => now(), 'updated_at' => now()],
            ['id' => '6','nama_fakultas_biro' => 'Kantor Admisi & Humas','kode_fakultas_biro' => 'KAH','created_at' => now(), 'updated_at' => now()],
            ['id' => '7','nama_fakultas_biro' => 'Kemahasiswaan','kode_fakultas_biro' => 'UCC','created_at' => now(), 'updated_at' => now()],
            ['id' => '8','nama_fakultas_biro' => 'UPT Sistem Informasi','kode_fakultas_biro' => 'UPT SI','created_at' => now(), 'updated_at' => now()]
       ];
       DataFakultasBiro::insert($records);
    }
}
