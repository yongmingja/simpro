<?php

use Illuminate\Database\Seeder;
use App\Models\Master\JabatanPegawai;

class JabatanPegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => '1','id_pegawai' => 3,'id_jabatan' => 1,'id_fakultas_biro' => null,'ket_jabatan' => 'Superadmin','created_at' => now(), 'updated_at' => now()],
            ['id' => '2','id_pegawai' => 3,'id_jabatan' => 2,'id_fakultas_biro' => null,'ket_jabatan' => 'Admin Umum','created_at' => now(), 'updated_at' => now()],
            ['id' => '3','id_pegawai' => 3,'id_jabatan' => 5,'id_fakultas_biro' => 2,'ket_jabatan' => 'Dekan Fakultas Komputer','created_at' => now(), 'updated_at' => now()],
            ['id' => '4','id_pegawai' => 3,'id_jabatan' => 6,'id_fakultas_biro' => null,'ket_jabatan' => 'Dosen / Staff','created_at' => now(), 'updated_at' => now()],
            ['id' => '5','id_pegawai' => 221,'id_jabatan' => 6,'id_fakultas_biro' => 8,'ket_jabatan' => 'UPT SI','created_at' => now(), 'updated_at' => now()],
            ['id' => '6','id_pegawai' => 35,'id_jabatan' => 4,'id_fakultas_biro' => null,'ket_jabatan' => 'WRSDP','created_at' => now(), 'updated_at' => now()],
            ['id' => '7','id_pegawai' => 78,'id_jabatan' => 4,'id_fakultas_biro' => null,'ket_jabatan' => 'WRAK','created_at' => now(), 'updated_at' => now()],
            ['id' => '8','id_pegawai' => 226,'id_jabatan' => 5,'id_fakultas_biro' => 8,'ket_jabatan' => 'Kepala UPT SI','created_at' => now(), 'updated_at' => now()],
            ['id' => '9','id_pegawai' => 54,'id_jabatan' => 3,'id_fakultas_biro' => null,'ket_jabatan' => 'Rektor Universitas Universal','created_at' => now(), 'updated_at' => now()],
            ['id' => '10','id_pegawai' => 39,'id_jabatan' => 5,'id_fakultas_biro' => 2,'ket_jabatan' => 'Dekan Fakultas Komputer','created_at' => now(), 'updated_at' => now()],
            ['id' => '11','id_pegawai' => 39,'id_jabatan' => 6,'id_fakultas_biro' => 2,'ket_jabatan' => 'Dosen Fakultas Komputer','created_at' => now(), 'updated_at' => now()],
            ['id' => '12','id_pegawai' => 66,'id_jabatan' => 5,'id_fakultas_biro' => 6,'ket_jabatan' => 'Kepala Kantor Admisi & Humas','created_at' => now(), 'updated_at' => now()],
            ['id' => '13','id_pegawai' => 83,'id_jabatan' => 5,'id_fakultas_biro' => 7,'ket_jabatan' => 'Kepala UCC','created_at' => now(), 'updated_at' => now()],
            ['id' => '14','id_pegawai' => 226,'id_jabatan' => 6,'id_fakultas_biro' => null,'ket_jabatan' => 'UPT SI','created_at' => now(), 'updated_at' => now()],
            ['id' => '15','id_pegawai' => 78,'id_jabatan' => 6,'id_fakultas_biro' => 2,'ket_jabatan' => 'Dosen Fakultas Komputer','created_at' => now(), 'updated_at' => now()],
            ['id' => '16','id_pegawai' => 218,'id_jabatan' => 2,'id_fakultas_biro' => null,'ket_jabatan' => 'Kepala Admin Umum','created_at' => now(), 'updated_at' => now()],
            ['id' => '17','id_pegawai' => 243,'id_jabatan' => 6,'id_fakultas_biro' => 7,'ket_jabatan' => 'Ketua Himpunan Mahasiswa Komputer','created_at' => now(), 'updated_at' => now()],
       ];
       JabatanPegawai::insert($records);
    }
}
