<?php

use Illuminate\Database\Seeder;
use App\Models\Master\Pegawai;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            [
                'id' => '1',
                'user_id' => '1000',
                'nama_pegawai' => 'Superadmin',
                'email' => 'simpro@admin.com',
                'password' => Hash::make('11111111'), 
                'jenis_kelamin' => 'L', 
                'tanggal_lahir' => now(), 
                'id_status_pegawai' => 1, 
                'created_at' => now(),
                'updated_at' => now()
            ]
       ];
       Pegawai::insert($records);
    }
}
