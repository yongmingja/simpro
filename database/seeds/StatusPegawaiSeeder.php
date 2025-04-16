<?php

use Illuminate\Database\Seeder;
use App\Models\Master\StatusPegawai;

class StatusPegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => '1','keterangan' => 'Aktif','created_at' => now(), 'updated_at' => now()],
            ['id' => '2','keterangan' => 'Cuti','created_at' => now(), 'updated_at' => now()],
            ['id' => '3','keterangan' => 'Mengundurkan Diri','created_at' => now(), 'updated_at' => now()],
            ['id' => '4','keterangan' => 'Honorer','created_at' => now(), 'updated_at' => now()]
       ];
       StatusPegawai::insert($records);
    }
}
