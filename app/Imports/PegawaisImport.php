<?php

namespace App\Imports;

use App\Models\Master\Pegawai;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PegawaisImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */

    public function collection(Collection $rows)
    {
        foreach($rows as $row){
            // Check nip already exists
           $count = Pegawai::where('nip',$row['nip'])->count();
           if($count > 0){
              Pegawai::where('nip',$row['nip'])->update([
                'nama_pegawai'          => $row['nama_pegawai'],
                'nip'                   => $row['nip'],
                'email'                 => $row['alamat_email'], 
                'password'              => Hash::make(date('Ymd',strtotime($row['tanggal_lahir']))),
                'jenis_kelamin'         => $row['jenis_kelamin'],
                'agama'                 => $row['agama'],
                'id_status_pegawai'     => $row['status']
              ]);
           } else {
               Pegawai::create([
                   'nama_pegawai'          => $row['nama_pegawai'],
                   'nip'                   => $row['nip'],
                   'email'                 => $row['alamat_email'], 
                   'password'              => Hash::make(date('Ymd',strtotime($row['tanggal_lahir']))),
                   'jenis_kelamin'         => $row['jenis_kelamin'],
                   'agama'                 => $row['agama'],
                   'id_status_pegawai'     => $row['status'],
               ]);
           }
        }

    }

    public function headingRow(): int {
        return 1;
     }
}
