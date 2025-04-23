<html>

<head>
    <title>Data Proposal dan Laporan Kegiatan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            border: 1px solid #ddd;
            padding: 8px;
        }
        td {
            word-wrap: break-word; /* Ensures wrapping for long words */
            word-break: break-word; /* Handles wrapping when there's a continuous long word */
            white-space: normal; /* Allows text wrapping within the cell */
            text-align: left; /* Align text to the left for better readability */
        }
    </style>
    
</head>
<body>
    <div class="container-fluid p-3">
        <div class="row p-3 bg-transparent rounded">
            <div class="col-sm-12">
                    <h4 class="mt-3">
                        <center>Data Proposal dan Laporan Pertanggungjawaban Kegiatan</center>
                    </h4>                
                    <h5 class="mb-3">
                        <center>Tahun Akademik {{$getYear}}</center>
                    </h5>
                <table class="table table-bordered table-hover mt-2 table-sm">
                    <thead>
                        <tr>
                            <th style="vertical-align: middle; text-align: center;">#</th>
                            <th style="vertical-align: middle; text-align: center;">Judul Kegiatan</th>
                            <th style="vertical-align: middle; text-align: center;">Tgl Kegiatan</th>
                            <th style="vertical-align: middle; text-align: center;">Tgl Pengajuan</th>
                            <th style="vertical-align: middle; text-align: center;">Ketua Pelaksana</th>
                            <th style="vertical-align: middle; text-align: center;">Unit Penyelenggara</th>
                            <th style="vertical-align: middle; text-align: center;">Kode Renstra</th>
                            <th style="vertical-align: middle; text-align: center;">Kode Akun</th>
                            <th style="vertical-align: middle; text-align: center;">Anggaran RKAT</th>
                            <th style="vertical-align: middle; text-align: center;">Anggaran Proposal</th>
                            <th style="vertical-align: middle; text-align: center;">Realisasi Anggaran</th>
                            <th style="vertical-align: middle; text-align: center;">Status Laporan</th>
                            <th style="vertical-align: middle; text-align: center;">Link Laporan</th>
                            <th style="vertical-align: middle; text-align: center;">Link Proposal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($datas as $data)
                        <tr>
                            <td style="vertical-align: middle; text-align: center;">{{$no}}</td>
                            <td>{{$data->nama_kegiatan}}</td>
                            <td style="vertical-align: middle; text-align: center;">{{tanggal_indonesia($data->tgl_event)}}</td>
                            <td style="vertical-align: middle; text-align: center;">{{tanggal_indonesia($data->created_at)}}</td>
                            <td style="vertical-align: middle; text-align: center;">{{$data->nama_pegawai}}</td>
                            <td style="vertical-align: middle; text-align: center;">{{$data->nama_fakultas_biro}}</td>
                            <td style="vertical-align: middle; text-align: center;">{{$data->kode_renstra}}</td>
                            <td></td>
                            <td style="vertical-align: middle; text-align: right;">{{currency_IDR($data->total)}}</td>
                            <td style="vertical-align: middle; text-align: right;">{{currency_IDR($data->anggaran_proposal)}}</td>
                            <td style="vertical-align: middle; text-align: right;">{{currency_IDR($data->realisasi_anggaran)}}</td>
                            <td style="vertical-align: middle; text-align: center;">
                            @if($data->is_archived != 1)
                                @if(!empty($data->status_approval))
                                    @switch($data->status_approval)
                                        @case(5)
                                            ACC Rektorat
                                            @break
                                        @case(4)
                                            Ditolak Rektorat
                                            @break
                                        @case(3)
                                            Menunggu validasi rektorat
                                            @break
                                        @case(2)
                                            Ditolak Atasan
                                            @break
                                        @default
                                            Menunggu validasi atasan
                                    @endswitch
                                @else
                                    Belum ada laporan
                                @endif
                            @else
                                Dibatalkan oleh user
                            @endif
                            </td>
                            <td style="vertical-align: middle;">
                            @if($data->is_archived != 1)
                                @if(!empty($data->status_approval))
                                    @switch($data->status_approval)
                                        @case(5)
                                        <a href="{{''.URL::to('/').'/preview-laporan-proposal'.'/'.encrypt($data->id_laporan_proposal)}}" target="_blank">Lihat laporan <i class="bx bx-link bx-xs"></i></a>
                                            @break
                                        @case(4)
                                            Ditolak Rektorat
                                            @break
                                        @case(3)
                                            Menunggu validasi rektorat
                                            @break
                                        @case(2)
                                            Ditolak Atasan
                                            @break
                                        @default
                                            Menunggu validasi atasan
                                    @endswitch
                                @else
                                    Belum ada laporan
                                @endif
                            @else
                                Dibatalkan oleh user
                            @endif
                            </td> 
                            <td><a href="{{''.URL::to('/').'/preview-proposal'.'/'.encrypt($data->id)}}" target="_blank">Lihat proposal <i class="bx bx-link bx-xs"></i></a></td>                           
                        </tr>
                        @php $no++ @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>