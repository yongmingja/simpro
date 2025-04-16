<?php

use Illuminate\Database\Seeder;
use App\Models\Master\ValidatorProposal;

class ValidatorProposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => '1','diusulkan_oleh' => 6,'diketahui_oleh' => 5,'created_at' => now(), 'updated_at' => now()],
            ['id' => '2','diusulkan_oleh' => 4,'diketahui_oleh' => 3,'created_at' => now(), 'updated_at' => now()]
       ];
       ValidatorProposal::insert($records);
    }
}
