<html>

<head>
    <title>Data dan Laporan FPKU</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <div class="container-fluid p-3">
        <div class="row p-3 bg-transparent rounded">
            <div class="col-sm-12">
                    <h4 class="mt-3">
                        <center>Data dan Laporan FPKU</center>
                    </h4>                
                    <h5 class="mb-3">
                        <center>Tahun {{$getYear}}</center>
                    </h5>
                <table class="table table-bordered table-hover mt-2 table-sm">
                    <thead>
                        <tr>
                            <th style="vertical-align: middle; text-align: center;">#</th>
                            <th style="vertical-align: middle; text-align: center;">No. FPKU</th>
                            <th style="vertical-align: middle; text-align: center;">Nama Kegiatan</th>
                            <th style="vertical-align: middle; text-align: center;">Tgl Kegiatan</th>
                            <th style="vertical-align: middle; text-align: center;">Ketua Pelaksana</th>
                            <th style="vertical-align: middle; text-align: center;">Anggota Pelaksana</th>
                            <th style="vertical-align: middle; text-align: center;">Undangan</th>
                            <th style="vertical-align: middle; text-align: center;">Status Laporan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($datas as $data)
                        <tr>
                            <td style="text-align: center;">{{$no}}</td>
                            <td>{{$data->no_surat_undangan}}</td>
                            <td>{{$data->nama_kegiatan}}</td>
                            <td style="text-align: center;">{{tanggal_indonesia($data->tgl_kegiatan)}}</td>
                            <td style="text-align: center;">{{$data->ketua}}</td>
                            @php 
                            $dataPegawai = \App\Models\Master\Pegawai::whereIn('id',$data->peserta_kegiatan)->select('nama_pegawai')->get();
                            $pegawai = [];
                            foreach($dataPegawai as $result){
                                $pegawai[] = $result->nama_pegawai;                                
                            }
                            @endphp
                            <td>{!! implode(", <br>",$pegawai) !!}</td>
                            <td><a href="{{$data->link_gdrive}}">{{$data->link_gdrive}}</a></td>
                            <td style="text-align: center;">@if(!empty($data->status_approval)) @if($data->status_approval == 5) verified by WR @else Belum ada laporan @endif @endif</td>

                            
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