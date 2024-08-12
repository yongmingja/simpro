<?php

use Illuminate\Database\Seeder;
use App\Models\General\JenisKegiatan;

class JenisProposalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => '1','nama_jenis_kegiatan' => 'RKAT','created_at' => now(), 'updated_at' => now()],
            ['id' => '2','nama_jenis_kegiatan' => 'Non-RKAT','created_at' => now(), 'updated_at' => now()],
            ['id' => '3','nama_jenis_kegiatan' => 'FKPU','created_at' => now(), 'updated_at' => now()]
       ];
       JenisKegiatan::insert($records);
    }
}
