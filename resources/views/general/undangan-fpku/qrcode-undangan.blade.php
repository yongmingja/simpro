<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>QR Code - Undangan</title>
    <link href="{{asset('assets/css/css-export/style-bootstrap.css')}}" rel="stylesheet">
</head>
<style>
    .container {
        margin-top: 150px;
    }
</style>
<body>
<div class="container">
    <div class="col-sm-12 mt-3">
        <div class="row">
            <div class="card p-3">
                <div class="card-body">
                    @if($datas->count() > 0)
                    <table class="table table-hover table-bordered">
                        <tr>
                            <td colspan="2" style="text-align: center;"><b>Verifikasi Undangan<br>Form Partisipasi Kegiatan Undangan<br>Universitas Universal</b></td>
                        </tr>
                        @foreach($datas as $data)
                        <tr>
                            <td>Undangan Dari</td>
                            <td>{{$data->undangan_dari}}</td>
                        </tr>
                        <tr>
                            <td>Nama Kegiatan</td>
                            <td>{{$data->nama_kegiatan}}</td>
                        </tr>
                        <tr>
                            <td>Tanggal Kegiatan</td>
                            <td>{{tanggal_indonesia($data->tgl_kegiatan)}}</td>
                        </tr>
                        <tr>
                            <td>Peserta Kegiatan</td>
                            @php 
                            $dataPegawai = \App\Models\Master\Pegawai::whereIn('id',$data->peserta_kegiatan)->select('nama_pegawai')->get();
                            foreach($dataPegawai as $result){
                                $pegawai[] = $result->nama_pegawai;
                                
                            } @endphp
                            <td>{{implode(", ",$pegawai)}}</td>
                        </tr>
                        @endforeach                       
                        <tr>
                            <td>Wakil Rektor <br>Bidang Sumber Daya Pengembangan</td>
                            <td style="vertical-align: middle;">Benny Roesly, S.T., M.Pd.</td>
                        </tr>
                    </table>
                    <p style="text-align: center;">&reg;adalah benar dan tercatat dalam sistem kami.</p>
                    @else
                    <p style="text-align: center;"><b>Invalid QR! Tidak tercatat dalam sistem.</b></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>