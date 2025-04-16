<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call([
            DataFakultasBiroTableSeeder::class,
            DataProdiBiroTableSeeder::class,
            JenisProposalTableSeeder::class,
            HandleProposalSeeder::class,
            ValidatorProposalSeeder::class,
            JabatanSeeder::class,
            JabatanPegawaiSeeder::class,
            StatusPegawaiSeeder::class,
            TahunAkademikSeeder::class,
        ]);
    }
}
