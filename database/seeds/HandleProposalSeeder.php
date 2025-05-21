<?php

use Illuminate\Database\Seeder;
use App\Models\Master\HandleProposal;

class HandleProposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => '1','id_pegawai' => 1,'id_jenis_kegiatan' => '["1"]','created_at' => now(), 'updated_at' => now()],
            ['id' => '2','id_pegawai' => 1,'id_jenis_kegiatan' => '["2"]','created_at' => now(), 'updated_at' => now()]
       ];
       HandleProposal::insert($records);
    }
}
