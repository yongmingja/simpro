<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>QR Code - Approval State</title>
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
                    <table class="table table-hover table-bordered">
                        @foreach($datas as $data)
                        <tr>
                            <td colspan="2" style="text-align: center;"><b>Verifikasi Lembar Pengesahan Proposal Kegiatan<br>Universitas Universal</b></td>
                        </tr>
                        <tr>
                            <td>Nama Kegiatan</td>
                            <td>{{$data->nama_kegiatan}}</td>
                        </tr>
                        <tr>
                            <td>Tanggal Kegiatan</td>
                            <td>{{tanggal_indonesia($data->tgl_event)}}</td>
                        </tr>
                        <tr>
                            <td>Penyusun Proposal</td>
                            <td>{{$getPengusul->nama_pegawai}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Bahwa Proposal ini telah diketahui dan disetujui oleh:</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td>Diketahui oleh @if($getDiketahui->ket_jabatan != '') {{$getDiketahui->ket_jabatan}} @else {{$getDiketahui->nama_jabatan}} @endif</td>
                            <td>{{$getDiketahui->nama_pegawai}}</td>
                        </tr>                    
                        <tr>
                            <td>@if($getDisetujui->ket_jabatan != '') {{$getDisetujui->ket_jabatan}} @else {{$getDisetujui->nama_jabatan}} @endif</td>
                            <td style="vertical-align: middle;">{{$getDisetujui->nama_pegawai}}</td>
                        </tr>
                        @foreach($datas as $tgl)
                        <tr>
                            <td>Tanggal Pengesahan</td>
                            <td>{{tanggal_indonesia($tgl->updated_at)}}</td>
                        </tr>
                        @endforeach
                    </table>
                    <p style="text-align: center;">&reg;adalah benar dan tercatat dalam sistem kami.</p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>