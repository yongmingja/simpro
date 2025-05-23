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
            PegawaiSeeder::class,
            JenisProposalTableSeeder::class,
            DataFakultasBiroSeeder::class,
            DataProdiBiroSeeder::class,
            HandleProposalSeeder::class,
            ValidatorProposalSeeder::class,
            JabatanSeeder::class,
            JabatanPegawaiSeeder::class,
            StatusPegawaiSeeder::class,
            TahunAkademikSeeder::class,
        ]);
    }
}
