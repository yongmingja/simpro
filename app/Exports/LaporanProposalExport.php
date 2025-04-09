<?php

namespace App\Exports;

use App\Models\General\LaporanProposal;
use App\Models\General\Proposal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB; use URL;

class LaporanProposalExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
            'Tahun Akademik',
            'Judul Kegiatan',
            'Tgl Kegiatan',
            'Tgl Pengajuan',
            'Ketua Pelaksana',
            'Unit Penyelenggara',
            'Kode Renstra',
            'Kode Akun',
            'Anggaran RKAT',
            'Anggaran Proposal',
            'Realisasi Anggaran',
            'Status Laporan',
            'Link Laporan',
            'Link Proposal'
        ];
    }

    public function collection()
    {
        $getYear = $this->getYear;
        if($getYear == null || $getYear == '[semua]'){
            $datas = Proposal::leftJoin('pegawais', 'pegawais.user_id', '=', 'proposals.user_id')
                ->leftJoin('status_laporan_proposals', 'status_laporan_proposals.id_laporan_proposal', '=', 'proposals.id')
                ->leftJoin('data_fakultas_biros', 'data_fakultas_biros.id', '=', 'proposals.id_fakultas_biro')
                ->leftJoin('form_rkats', 'form_rkats.id', '=', 'proposals.id_form_rkat')
                ->leftJoin('data_rencana_anggarans', 'data_rencana_anggarans.id_proposal', '=', 'proposals.id')
                ->leftJoin('data_realisasi_anggarans', 'data_realisasi_anggarans.id_proposal', '=', 'proposals.id')
                ->leftJoin('tahun_akademiks','tahun_akademiks.id','=','proposals.id_tahun_akademik')
                ->select(
                    'proposals.id AS id',
                    'proposals.nama_kegiatan',
                    'proposals.tgl_event',
                    'proposals.created_at',
                    'pegawais.nama_pegawai',
                    'status_laporan_proposals.status_approval',
                    'data_fakultas_biros.nama_fakultas_biro',
                    'form_rkats.kode_renstra',
                    'form_rkats.total',
                    'tahun_akademiks.year',
                    DB::raw('(SELECT SUM(data_rencana_anggarans.biaya_satuan * data_rencana_anggarans.quantity * data_rencana_anggarans.frequency) FROM data_rencana_anggarans WHERE data_rencana_anggarans.id_proposal = proposals.id) as anggaran_proposal'),
                    DB::raw('(SELECT SUM(data_realisasi_anggarans.biaya_satuan * data_realisasi_anggarans.quantity * data_realisasi_anggarans.frequency) FROM data_realisasi_anggarans WHERE data_realisasi_anggarans.id_proposal = proposals.id) as realisasi_anggaran')
                )
                ->groupBy(
                    'proposals.id',
                    'proposals.nama_kegiatan',
                    'proposals.tgl_event',
                    'proposals.created_at',
                    'pegawais.nama_pegawai',
                    'status_laporan_proposals.status_approval',
                    'data_fakultas_biros.nama_fakultas_biro',
                    'form_rkats.kode_renstra',
                    'form_rkats.total',
                    'tahun_akademiks.year'
                )
                ->orderBy('proposals.tgl_event','DESC')
                ->get();

        } else {
            $datas = Proposal::leftJoin('pegawais', 'pegawais.user_id', '=', 'proposals.user_id')
                ->leftJoin('status_laporan_proposals', 'status_laporan_proposals.id_laporan_proposal', '=', 'proposals.id')
                ->leftJoin('data_fakultas_biros', 'data_fakultas_biros.id', '=', 'proposals.id_fakultas_biro')
                ->leftJoin('form_rkats', 'form_rkats.id', '=', 'proposals.id_form_rkat')
                ->leftJoin('data_rencana_anggarans', 'data_rencana_anggarans.id_proposal', '=', 'proposals.id')
                ->leftJoin('data_realisasi_anggarans', 'data_realisasi_anggarans.id_proposal', '=', 'proposals.id')
                ->leftJoin('tahun_akademiks','tahun_akademiks.id','=','proposals.id_tahun_akademik')
                ->select(
                    'proposals.id AS id',
                    'proposals.nama_kegiatan',
                    'proposals.tgl_event',
                    'proposals.created_at',
                    'pegawais.nama_pegawai',
                    'status_laporan_proposals.status_approval',
                    'data_fakultas_biros.nama_fakultas_biro',
                    'form_rkats.kode_renstra',
                    'form_rkats.total',
                    'tahun_akademiks.year',
                    DB::raw('(SELECT SUM(data_rencana_anggarans.biaya_satuan * data_rencana_anggarans.quantity * data_rencana_anggarans.frequency) FROM data_rencana_anggarans WHERE data_rencana_anggarans.id_proposal = proposals.id) as anggaran_proposal'),
                    DB::raw('(SELECT SUM(data_realisasi_anggarans.biaya_satuan * data_realisasi_anggarans.quantity * data_realisasi_anggarans.frequency) FROM data_realisasi_anggarans WHERE data_realisasi_anggarans.id_proposal = proposals.id) as realisasi_anggaran')
                )
                ->groupBy(
                    'proposals.id',
                    'proposals.nama_kegiatan',
                    'proposals.tgl_event',
                    'proposals.created_at',
                    'pegawais.nama_pegawai',
                    'status_laporan_proposals.status_approval',
                    'data_fakultas_biros.nama_fakultas_biro',
                    'form_rkats.kode_renstra',
                    'form_rkats.total',
                    'tahun_akademiks.year'
                )
                ->where('proposals.id_tahun_akademik',$getYear)
                ->orderBy('proposals.tgl_event','DESC')
                ->get();
        }

        return $array = $datas->map(function ($value, $key) {
            $statuses = [
                5 => ['status' => 'ACC Rektorat', 'link' => ''.URL::to('/').'/preview-laporan-proposal'.'/'.encrypt($value->id)],
                4 => ['status' => 'Ditolak Rektorat', 'link' => 'Ditolak Rektorat'],
                3 => ['status' => 'Menunggu validasi rektorat', 'link' => 'Menunggu validasi rektorat'],
                2 => ['status' => 'Ditolak Atasan', 'link' => 'Ditolak Atasan'],
                1 => ['status' => 'Menunggu validasi atasan', 'link' => 'Menunggu validasi atasan'],
            ];
            
            if (isset($statuses[$value->status_approval])) {
                $statusLaporan = $statuses[$value->status_approval]['status'];
                $linkLaporan = $statuses[$value->status_approval]['link'];
            } else {
                $statusLaporan = 'Belum ada laporan';
                $linkLaporan = 'Belum ada laporan';
            }            

            static $no = 1;
            
            return [
                'No' => $no++,
                'Tahun Akademik' => $value->year,
                'Judul Kegiatan' => $value->nama_kegiatan,
                'Tgl Kegiatan' => tanggal_indonesia($value->tgl_event),
                'Tgl Pengajuan' => tanggal_indonesia($value->created_at),
                'Ketua Pelaksana' => $value->nama_pegawai,
                'Unit Penyelenggara' => $value->nama_fakultas_biro,
                'Kode Renstra' => $value->kode_renstra,
                'Kode Akun' => '-',
                'Anggaran RKAT' => currency_IDR($value->total),
                'Anggaran Proposal' => currency_IDR($value->anggaran_proposal),
                'Realisasi Anggaran' => currency_IDR($value->realisasi_anggaran),
                'Status Laporan' => $statusLaporan,
                'Link Laporan' => $linkLaporan,
                'Link Proposal' => ''.URL::to('/').'/preview-proposal'.'/'.encrypt($value->id)
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
   
                $event->sheet->getDelegate()->getStyle('A1:O1')
                                ->getFont()
                                ->setBold(true);
            },
        ];
    }
}
