<?php

namespace App\Imports;

use App\Setting\Mahasiswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class MahasiswasImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Mahasiswa([
            'name'     => $row['name'],
            'user_id'  => $row['user_id'],
            'email'    => $row['email'], 
            'password' => Hash::make(str_replace('/', '', $row['password'])),
        ]);
    }
}
