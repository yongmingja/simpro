<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Preview Undangan FPKU</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    @page {
        margin-left: 100px;
    }
    body {
        margin-right: 20px;
        line-height: 25px;
    }
    .judul {
        text-align: center;
        margin-top: 10px;
    }
    .isi-undangan {
        margin-bottom: 2em;
    }
    .catatan {
        margin-bottom: 2em;
    }
    .th_keperluan {
        border: 0.5px solid black;
        border-collapse: collapse;
    }
    .td_keperluan {
        border: 0.5px solid black;
        border-collapse: collapse;
    }
    .td_titik {
       padding-left: 30px;
    }
    .td_isi_undangan {
        padding-left: 10px;
    }
    .terkait_undangan {
        margin-top: 2em;
    }
</style>
<body>
    <div class="header">
        <img src="{{public_path('assets/img/head.png')}}" alt="head" style="height:40px;">
    </div>
    <div class="judul">
        <h4>FORM PARTISIPASI KEGIATAN UNDANGAN</h4>
        <h4>@foreach($dataUndangan as $no) {{$no->no_surat_undangan}} @endforeach</h4>
    </div>
    <div class="terkait_undangan">
        <p>Terkait dengan undangan,</p>
    </div>
    <div class="isi-undangan">
        <table>
            <tr>
                <td style="vertical-align: top;">Dari</td>
                <td style="vertical-align: top;" class="td_titik">:</td>
                <td class="td_isi_undangan">@foreach($dataUndangan as $dari) {{$dari->undangan_dari}} @endforeach</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Nama Kegiatan</td>
                <td style="vertical-align: top;" class="td_titik">:</td>
                <td class="td_isi_undangan">@foreach($dataUndangan as $namaKegiatan) {{$namaKegiatan->nama_kegiatan}} @endforeach</td>
            </tr>
            <tr>
                <td width="120px;">Tanggal Kegiatan</td>
                <td class="td_titik">:</td>
                <td class="td_isi_undangan">@foreach($dataUndangan as $tglKegiatan) {{tanggal_indonesia($tglKegiatan->tgl_kegiatan)}} @endforeach</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Peserta Kegiatan</td>
                <td class="td_titik" style="vertical-align: top;">:</td>
                <td class="td_isi_undangan">@foreach($dataUndangan as $peserta) @php $dataPegawai = \App\Models\Master\Pegawai::whereIn('id',$peserta->peserta_kegiatan)->select('nama_pegawai')->get(); 
                foreach($dataPegawai as $result){
                    $pegawai[] = $result->nama_pegawai;                    
                } @endphp {!!implode(", <br>",$pegawai)!!} @endforeach</td>
            </tr>
        </table>
    </div>
    <div>
        <p>Keperluan yang perlu dipersiapkan:</p>
    </div>
    <div class="keperluan">
        <table class="table table-bordered" id="table-keperluan">
            <thead>
                <tr>
                    <th style="vertical-align: middle; text-align: center;" class="th_keperluan">No</th>
                    <th style="vertical-align: middle; text-align: center;" class="th_keperluan"><b>Keperluan</b><br>(nama barang, jenis pelayanan, waktu, jumlah, detil lain)</th>
                </tr>
            </thead>
            <tbody>

                @if($dataKeperluan->count() > 0)
                @foreach($dataKeperluan as $no => $keperluan)
                <tr>
                    <td style="text-align: center;" class="td_keperluan">{{++$no}}.</td>
                    <td class="td_keperluan" style="padding-left: 10px;">{{$keperluan->isi_keperluan}}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="2" style="text-align: center;" class="td_keperluan">Tidak ada data keperluan</td>
                </tr>
                @endif
            </tbody>
            
        </table>
    </div>
    <div class="catatan">
        <table>
            <tr>
                <td width="120px;">Catatan</td>
                <td class="td_titik">:</td>
                <td class="td_isi_undangan">@foreach($dataUndangan as $ctt) {{$ctt->catatan}} @endforeach</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <table class="table table-bordered">
            @if($verifiedQrCode->count() > 0)
                @foreach($verifiedQrCode as $qr)
                <tr>
                    <td><br>Menyetujui,<br>Wakil Rektor Sumber Daya dan Pengembangan <br><img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(80)->errorCorrection('H')->generate($qr->generate_qrcode)) !!}" style="margin-top: 1.5em;"><br>Benny Roesly, M.Pd</td>
                    <td>Batam, @foreach($dataUndangan as $tanggal) {{tanggal_indonesia($tanggal->created_at)}} @endforeach <br>Yang membuat <br>Staf Adminitrasi dan Umum <br><br><img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(80)->errorCorrection('H')->generate($qr->generate_qrcode)) !!}"><br>@foreach($dataUndangan as $nmUser) {{$nmUser->nama_pegawai}} @endforeach</td>
                </tr>
                @endforeach
            @else
            <tr>
                <td><br>Menyetujui,<br>Wakil Rektor Sumber Daya dan Pengembangan <br><p style="margin-top: 5em;"></p>Benny Roesly, M.Pd</td>
                <td>Batam, @foreach($dataUndangan as $tanggal) {{tanggal_indonesia($tanggal->created_at)}} @endforeach <br>Yang membuat <br>Staf Adminitrasi dan Umum <br><img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(80)->errorCorrection('H')->generate('Not verified by WRSDP!')) !!}" style="margin-top: 1em;"><br>@foreach($dataUndangan as $nmUser) {{$nmUser->nama_pegawai}} @endforeach</td>
            </tr>
            @endif
        </table>
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>