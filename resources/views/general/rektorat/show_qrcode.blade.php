<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>QR Code - Proposal Report</title>
    <link href="{{asset('assets/css/css-export/style-bootstrap.css')}}" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="col-sm-12 mt-3">
        <div class="row">
            <div class="card p-3">
                <div class="card-body">
                    <table class="table table-hover table-bordered">
                        <tr>
                            <td colspan="2" style="text-align: center;"><b>Verifikasi Lembar Pengesahan<br>Laporan Pertanggung-jawaban Kegiatan<br>Universitas Universal</b></td>
                        </tr>
                        @foreach($datas as $data)
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
                            <td>{{$data->nama_dosen}}{{$data->nama_mahasiswa}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Bahwa laporan pertanggung-jawaban kegiatan ini telah diketahui dan disetujui oleh:</td>
                        </tr>
                        @endforeach                       
                        <tr>
                            <td>Wakil Rektor</td>
                            <td>@foreach($datas as $wr) @if($wr->nama_jenis_kegiatan == 'RKAT') Yodi, S.Kom., M.S.I @elseif($wr->nama_jenis_kegiatan == 'Non-RKAT') Benny Roesly, S.T., M.Pd @else Yodi, S.Kom., M.S.I /<br> Benny Roesly, S.T., M.Pd @endif @endforeach</td>
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