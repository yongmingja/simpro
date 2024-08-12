<?php

use Illuminate\Database\Seeder;
use App\Models\General\DataProdi;

class DataProdiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => '1','id_fakultas'=> 3,'nama_prodi' => 'Seni Tari','kode_prodi' => 'ST','created_at' => now(), 'updated_at' => now()],
            ['id' => '2','id_fakultas'=> 3,'nama_prodi' => 'Seni Musik','kode_prodi' => 'SM','created_at' => now(), 'updated_at' => now()],
            ['id' => '3','id_fakultas'=> 1,'nama_prodi' => 'Manajemen','kode_prodi' => 'MN','created_at' => now(), 'updated_at' => now()],
            ['id' => '4','id_fakultas'=> 1,'nama_prodi' => 'Akuntansi','kode_prodi' => 'AK','created_at' => now(), 'updated_at' => now()],
            ['id' => '5','id_fakultas'=> 2,'nama_prodi' => 'Teknik Informatika','kode_prodi' => 'TIF','created_at' => now(), 'updated_at' => now()],
            ['id' => '6','id_fakultas'=> 2,'nama_prodi' => 'Sistem Informasi','kode_prodi' => 'SI','created_at' => now(), 'updated_at' => now()],
            ['id' => '7','id_fakultas'=> 2,'nama_prodi' => 'Teknik Perangkat Lunak','kode_prodi' => 'TPL','created_at' => now(), 'updated_at' => now()],
            ['id' => '8','id_fakultas'=> 4,'nama_prodi' => 'Teknik Industri','kode_prodi' => 'TI','created_at' => now(), 'updated_at' => now()],
            ['id' => '9','id_fakultas'=> 4,'nama_prodi' => 'Teknik Lingkungan','kode_prodi' => 'TL','created_at' => now(), 'updated_at' => now()],
            ['id' => '10','id_fakultas'=> 5,'nama_prodi' => 'Pendidikan Bahasa Mandarin','kode_prodi' => 'PBM','created_at' => now(), 'updated_at' => now()]
       ];
       DataProdi::insert($records);
    }
}
