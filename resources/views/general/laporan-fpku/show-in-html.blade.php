<html>

<head>
    <title>Data dan Laporan FPKU</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid p-3">
        <div class="row p-3 bg-transparent rounded">
            <div class="col-sm-12">
                    <h4 class="mt-3">
                        <center>Data dan Laporan FPKU</center>
                    </h4>                
                    <h5 class="mb-3">
                        <center>Tahun Akademik {{$getYear}}</center>
                    </h5>
                <table class="table table-bordered table-hover mt-2 table-sm">
                    <thead>
                        <tr>
                            <th style="vertical-align: middle; text-align: center;">#</th>
                            <th style="vertical-align: middle; text-align: center;">Tahun Akademik</th>
                            <th style="vertical-align: middle; text-align: center;">No. FPKU</th>
                            <th style="vertical-align: middle; text-align: center;">Nama Kegiatan</th>
                            <th style="vertical-align: middle; text-align: center;">Tgl Kegiatan</th>
                            <th style="vertical-align: middle; text-align: center;">Ketua Pelaksana</th>
                            <th style="vertical-align: middle; text-align: center;">Anggota Pelaksana</th>
                            <th style="vertical-align: middle; text-align: center;">Undangan</th>
                            <th style="vertical-align: middle; text-align: center;">Status Laporan</th>
                            <th style="vertical-align: middle; text-align: center; text-wrap: wrap;" width="50%;">Link Laporan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($datas as $data)
                        <tr>
                            <td style="vertical-align: middle; text-align: center;">{{$no}}</td>
                            <td style="vertical-align: middle; text-align: center;">{{$data->year}}</td>
                            <td style="vertical-align: middle; text-align: center;">{{$data->no_surat_undangan}}</td>
                            <td style="vertical-align: middle;">{{$data->nama_kegiatan}}</td>
                            <td style="vertical-align: middle; text-align: center;">{{tanggal_indonesia($data->tgl_kegiatan)}}</td>
                            <td style="vertical-align: middle; text-align: center;">{{$data->ketua}}</td>
                            @php 
                            $dataPegawai = \App\Models\Master\Pegawai::whereIn('id',$data->peserta_kegiatan)->select('nama_pegawai')->get();
                            $pegawai = [];
                            foreach($dataPegawai as $result){
                                $pegawai[] = $result->nama_pegawai;                                
                            }
                            @endphp
                            <td style="vertical-align: middle; ">{!! implode(", <br>",$pegawai) !!}</td>
                            <td style="vertical-align: middle; text-align: center;">
                                @if($data->berkas != '') <a href="{{''.URL::to('/').'/'.$data->berkas}}" target="_blank">Lihat Undangan <i class="bx bx-link bx-xs"></i></a> 
                                @else 
                                <a href="{{$data->link_gdrive}}" target="_blank">Lihat Undangan <i class="bx bx-link bx-xs"></i></a> 
                                @endif
                            </td>
                            <td style="vertical-align: middle; text-align: center;">
                            @if(!empty($data->status_approval))
                                @switch($data->status_approval)
                                    @case(3)
                                        ACC Rektorat
                                        @break
                                    @case(2)
                                        Ditolak Rektorat
                                        @break
                                    @default
                                        Menunggu validasi rektorat
                                @endswitch
                            @else
                                Belum ada laporan
                            @endif
                            </td>
                            <td style="vertical-align: middle;">
                            @if(!empty($data->status_approval))
                                @switch($data->status_approval)
                                    @case(3)
                                        <a href="{{''.URL::to('/').'/preview-laporan-fpku'.'/'.encrypt($data->id_laporan)}}" target="_blank">Lihat laporan <i class="bx bx-link bx-xs"></i></a>
                                        @break
                                    @case(2)
                                        Ditolak Rektorat
                                        @break
                                    @default
                                        Menunggu validasi rektorat
                                @endswitch
                            @else
                                Belum ada laporan
                            @endif
                            </td>                            
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