<?php

namespace App\Imports;

use App\Setting\Dosen;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class DosensImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new Dosen([
            'name'     => $row['name'],
            'user_id'  => $row['user_id'],
            'email'    => $row['email'], 
            'password' => Hash::make(str_replace('-', '', $row['tanggal_lahir'])),
        ]);
    }
}
