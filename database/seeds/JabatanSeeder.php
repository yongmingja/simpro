<?php

use Illuminate\Database\Seeder;
use App\Models\Master\Jabatan;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => '1','kode_jabatan' => 'SADM','nama_jabatan' => 'Superadmin','warna_label' => 'bg-label-success','created_at' => now(), 'updated_at' => now()],
            ['id' => '2','kode_jabatan' => 'ADU','nama_jabatan' => 'Admin Umum','warna_label' => 'bg-label-secondary','created_at' => now(), 'updated_at' => now()],
            ['id' => '3','kode_jabatan' => 'RKT','nama_jabatan' => 'Rektor','warna_label' => 'bg-label-primary','created_at' => now(), 'updated_at' => now()],
            ['id' => '4','kode_jabatan' => 'WAREK','nama_jabatan' => 'Wakil Rektor','warna_label' => 'bg-label-primary','created_at' => now(), 'updated_at' => now()],
            ['id' => '5','kode_jabatan' => 'PEG','nama_jabatan' => 'Dekan / Kepala KAH / Kepala UCC / Kepala Biro','warna_label' => 'bg-label-info','created_at' => now(), 'updated_at' => now()],
            ['id' => '6','kode_jabatan' => 'PEGS','nama_jabatan' => 'Dosen / Kabag Admisi / Kabag Humas / Staf Biro','warna_label' => 'bg-label-warning','created_at' => now(), 'updated_at' => now()]
       ];
       Jabatan::insert($records);
    }
}
