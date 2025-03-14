<?php

namespace App\Exports;

use App\Models\General\DataFpku;
use App\Models\Master\Pegawai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanFpkuExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $getYear;

    public function __construct($getYear)
    {
        $this->getYear = $getYear;
    }

    public function headings():array{
        return[
            'No',
            'No. FPKU',
            'Nama Kegiatan',
            'Tgl Kegiatan',
            'Ketua Pelaksana',
            'Anggota Pelaksana',
            'Undangan',
            'Status Laporan'
        ];
    }

    public function collection()
    {
        $getYear = $this->getYear;
        if($getYear == null || $getYear == '[semua]'){
            $datas = DataFpku::leftJoin('pegawais','pegawais.id','=','data_fpkus.ketua')
                ->leftJoin('lampiran_fpkus','lampiran_fpkus.id_fpku','=','data_fpkus.id')
                ->leftJoin('laporan_fpkus','laporan_fpkus.id_fpku','=','data_fpkus.id')
                ->leftJoin('status_laporan_fpkus','status_laporan_fpkus.id_laporan_fpku','=','laporan_fpkus.id')
                ->select('data_fpkus.id AS id','data_fpkus.no_surat_undangan','data_fpkus.nama_kegiatan','data_fpkus.tgl_kegiatan','data_fpkus.peserta_kegiatan','pegawais.nama_pegawai as ketua','lampiran_fpkus.link_gdrive','status_laporan_fpkus.status_approval')
                ->get();
        } else {
            $datas = DataFpku::leftJoin('pegawais','pegawais.id','=','data_fpkus.ketua')
                ->leftJoin('lampiran_fpkus','lampiran_fpkus.id_fpku','=','data_fpkus.id')
                ->leftJoin('laporan_fpkus','laporan_fpkus.id_fpku','=','data_fpkus.id')
                ->leftJoin('status_laporan_fpkus','status_laporan_fpkus.id_laporan_fpku','=','laporan_fpkus.id')
                ->select('data_fpkus.id AS id','data_fpkus.no_surat_undangan','data_fpkus.nama_kegiatan','data_fpkus.tgl_kegiatan','data_fpkus.peserta_kegiatan','pegawais.nama_pegawai as ketua','lampiran_fpkus.link_gdrive','status_laporan_fpkus.status_approval')
                ->whereYear('data_fpkus.tgl_kegiatan',$getYear)
                ->get();
        }
        return $array = $datas->map(function ($value, $key) {
            static $no = 1;

            $statusLaporan = '';
            if($value->status_approval == 5) {
                $statusLaporan = 'verified by WR';
            } else {
                $statusLaporan = 'Belum ada laporan';
            }

            $dataPegawai = Pegawai::whereIn('id',$value->peserta_kegiatan)->select('nama_pegawai')->get();
            $peserta_kegiatan = [];
            foreach($dataPegawai as $result){
                $pegawai[] = $result->nama_pegawai;                
            }
            $peserta_kegiatan = implode(', ', $pegawai);            

            return [
                'No' => $no++,
                'No. FPKU' => $value->no_surat_undangan,
                'Nama Kegiatan' => $value->nama_kegiatan,
                'Tgl Kegiatan' => tanggal_indonesia($value->tgl_kegiatan),
                'Ketua Pelaksana' => $value->ketua,
                'Anggota Pelaksana' => $peserta_kegiatan,
                'Undangan' => $value->link_gdrive,
                'Status Laporan' => $statusLaporan
            ];
        });
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class  => function(BeforeExport $event) {
                $event->writer->setCreator('SimproAdministrator');
            },
            AfterSheet::class    => function(AfterSheet $event) {
   
                $event->sheet->getDelegate()->getStyle('A1:H1')
                                ->getFont()
                                ->setBold(true);
            },
        ];
    }
}
