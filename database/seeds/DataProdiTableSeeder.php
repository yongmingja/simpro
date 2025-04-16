<?php

use Illuminate\Database\Seeder;
use App\Models\General\DataProdiBiro;

class DataProdiBiroTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => '1','id_fakultas_biro'=> 1,'nama_prodi_biro' => 'Manajemen','kode_prodi_biro' => 'MN','created_at' => now(), 'updated_at' => now()],
            ['id' => '2','id_fakultas_biro'=> 1,'nama_prodi_biro' => 'Akuntansi','kode_prodi_biro' => 'AK','created_at' => now(), 'updated_at' => now()],
            ['id' => '3','id_fakultas_biro'=> 2,'nama_prodi_biro' => 'Teknik Informatika','kode_prodi_biro' => 'TIF','created_at' => now(), 'updated_at' => now()],
            ['id' => '4','id_fakultas_biro'=> 2,'nama_prodi_biro' => 'Sistem Informasi','kode_prodi_biro' => 'SI','created_at' => now(), 'updated_at' => now()],
            ['id' => '5','id_fakultas_biro'=> 2,'nama_prodi_biro' => 'Teknik Perangkat Lunak','kode_prodi_biro' => 'TPL','created_at' => now(), 'updated_at' => now()],
            ['id' => '6','id_fakultas_biro'=> 3,'nama_prodi_biro' => 'Seni Tari','kode_prodi_biro' => 'ST','created_at' => now(), 'updated_at' => now()],
            ['id' => '7','id_fakultas_biro'=> 3,'nama_prodi_biro' => 'Seni Musik','kode_prodi_biro' => 'SM','created_at' => now(), 'updated_at' => now()],
            ['id' => '8','id_fakultas_biro'=> 4,'nama_prodi_biro' => 'Teknik Industri','kode_prodi_biro' => 'TI','created_at' => now(), 'updated_at' => now()],
            ['id' => '9','id_fakultas_biro'=> 4,'nama_prodi_biro' => 'Teknik Lingkungan','kode_prodi_biro' => 'TL','created_at' => now(), 'updated_at' => now()],
            ['id' => '10','id_fakultas_biro'=> 5,'nama_prodi_biro' => 'Pendidikan Bahasa Mandarin','kode_prodi_biro' => 'PBM','created_at' => now(), 'updated_at' => now()],
            ['id' => '11','id_fakultas_biro'=> 6,'nama_prodi_biro' => 'Biro Admisi & Humas','kode_prodi_biro' => '-','created_at' => now(), 'updated_at' => now()],
            ['id' => '12','id_fakultas_biro'=> 7,'nama_prodi_biro' => 'Uvers Career Center','kode_prodi_biro' => '-','created_at' => now(), 'updated_at' => now()],
            ['id' => '13','id_fakultas_biro'=> 8,'nama_prodi_biro' => 'UPT Sistem Informasi','kode_prodi_biro' => 'UPT SI','created_at' => now(), 'updated_at' => now()]
       ];
       DataProdiBiro::insert($records);
    }
}
