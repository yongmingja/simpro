<?php

namespace App\Imports;

use App\Models\Master\FormRkat;
use App\Models\General\TahunAkademik;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Auth;

class RkatsImport implements ToCollection, WithHeadingRow
{
    protected $idFakultasBiro;

    public function __construct($idFakultasBiro)
    {
        $this->idFakultasBiro = $idFakultasBiro;
    }
    
    public function collection(Collection $rows)
    {
        $getIdTahunAkademik = TahunAkademik::where('is_active',1)->select('id')->first();
        foreach($rows as $row){
            // Check data already exists
           $count = FormRkat::where([
                ['id_tahun_akademik', $getIdTahunAkademik->id],
                ['id_fakultas_biro', $this->idFakultasBiro],
                ['program_kerja', $row['program_kerja']],
                ['kode_renstra', $row['kode_renstra']],
                ['kode_pagu', $row['kode_pagu']],
                ['total', $row['total']]                
           ])->count();

           if($count > 0){
              FormRkat::where([
                    ['id_tahun_akademik', $getIdTahunAkademik->id],
                    ['program_kerja', $row['program_kerja']],
                    ['kode_renstra', $row['kode_renstra']],
                    ['kode_pagu', $row['kode_pagu']],
                    ['total', $row['total']]
              ])->update([
                    'id_tahun_akademik'     => $getIdTahunAkademik->id,
                    'id_fakultas_biro'      => $this->idFakultasBiro,
                    'sasaran_strategi'      => $row['sasaran_strategi'], 
                    'program_strategis'     => $row['program_strategis'], 
                    'program_kerja'         => $row['program_kerja'],
                    'kode_renstra'          => $row['kode_renstra'],
                    'nama_kegiatan'         => $row['nama_kegiatan'],
                    'penanggung_jawab'      => Auth::user()->id,
                    'kode_pagu'             => $row['kode_pagu'],
                    'total'                 => $row['total'],
                    'status_validasi'       => 0
              ]);
           } else {
               FormRkat::create([
                    'id_tahun_akademik'     => $getIdTahunAkademik->id,
                    'id_fakultas_biro'      => $this->idFakultasBiro,
                    'sasaran_strategi'      => $row['sasaran_strategi'], 
                    'program_strategis'     => $row['program_strategis'], 
                    'program_kerja'         => $row['program_kerja'],
                    'kode_renstra'          => $row['kode_renstra'],
                    'nama_kegiatan'         => $row['nama_kegiatan'],
                    'penanggung_jawab'      => Auth::user()->id,
                    'kode_pagu'             => $row['kode_pagu'],
                    'total'                 => $row['total'],
                    'status_validasi'       => 0
               ]);
           }
        }
    }

    public function headingRow(): int {
        return 1;
    }
}
