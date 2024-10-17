<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Preview Proposal</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    .body{
        font-size: 14px;
    }
    .table, tr, th, td{
        border: 0.5px solid black;
        border-collapse: collapse;
    }
    .tabel_th{
        text-align: center;
        padding: 5px 5px 5px 5px;
        vertical-align: middle;
    }
    .table_td{
        padding: 5px 5px 5px 5px;
        vertical-align: middle;
    }
    .table_thead{
        background-color: rgb(13, 149, 240);
        color: whitesmoke;
    }
    .footer_auditor, .trfooterauditor, .tdfooterauditor {
        border: 0;
        font-size: 12px;
        text-align: center;
    }
    .footer_auditor{
        margin-top: 4em;
    }
    .page-break {
        page-break-after: always;
        text-align: center;
    }
    .page-break-lampiran {
        page-break-before: always;
        text-align: center;
    }
    .cover_instansi {
        line-height: 8px;
        margin-top: 12em;
        font-style: bold;
    }
    .content_proposal {
        margin-left: 1em;
    }
</style>
<body>
    <div class="main-table">
        <div class="page-break">
            <p style="text-transform: uppercase; margin-top: 3em; font-style: bold;">PROPOSAL KEGIATAN</p>
            @foreach($datas as $kegiatan)
            <h3 style="text-transform: uppercase;">{{$kegiatan->nama_kegiatan}}</h3>
            <p>{{tanggal_indonesia($kegiatan->tgl_event)}}</p>
            @endforeach
            <img src="{{public_path('assets/img/logo-uvers.png')}}" alt="logo-uvers" style="margin-top: 8em;">
            <p style="margin-top: 3em;">Pengusul:</p>
            
            <p style="text-transform: uppercase;"><b> @foreach($datas as $pengusul) {{$pengusul->nama_user_dosen}}{{$pengusul->nama_user_mahasiswa}} @endforeach &ndash; @foreach($datas as $userid) {{$userid->user_id}} @endforeach</b></p>
            
            <div class="cover_instansi">
                @foreach($datas as $nama)
                <p style="text-transform: uppercase;">{{$nama->nama_prodi}}</p>
                <p style="text-transform: uppercase;">{{$nama->nama_fakultas}}</p>
                @endforeach
                <p style="text-transform: uppercase;">Universitas Universal</p>
                @foreach($datas as $tahun)
                <p style="text-transform: uppercase;">{{date('Y', strtotime($tahun->tgl_event))}}</p>
                @endforeach
            </div>

        </div>
        <div class="content_proposal">
            <h3 style="margin-top: 1.5em;">Pendahuluan</h3>
            @foreach($datas as $pendahuluan)
            <p style="text-align: justify;">{!!$pendahuluan->pendahuluan!!}</p>
            @endforeach

            <h3>Tujuan dan Manfaat</h3>
            @foreach($datas as $tujuan)
            <p style="text-align: justify;">{!!$tujuan->tujuan_manfaat!!}</p>
            @endforeach

            <h3>Peserta</h3>
            @foreach($datas as $peserta)
            <p style="text-align: justify;">{!!$peserta->peserta!!}</p>
            @endforeach

            <h3>Detil Kegiatan</h3>
            @foreach($datas as $detil)
            <p style="text-align: justify;">{!!$detil->detil_kegiatan!!}</p>
            @endforeach

            <h3>Keperluan Sarana Prasarana</h3>
            
            <table class="table table-bordered">
                <thead class="table_thead">
                    <tr>
                        <th class="tabel_th" width="2%">No</th>
                        <th class="tabel_th" width="5%">Tgl Kegiatan</th>
                        <th class="tabel_th" width="25%">Item</th>
                        <th class="tabel_th" width="3%">Jumlah</th>
                        <th class="tabel_th" width="5%">Sumber</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sarpras as $no => $item)
                    <tr>
                        <td style="text-align: center;" class="table_td">{{++$no}}</td>
                        <td style="text-align: center;" class="table_td">{{date('d-m-Y', strtotime($item->tgl_kegiatan))}}</td>
                        <td class="table_td">{{$item->sarpras_item}}</td>
                        <td style="text-align: center;" class="table_td">{{$item->jumlah}}</td>
                        <td style="text-align: center;" class="table_td">@if($item->sumber_dana == '1') Kampus @elseif($item->sumber_dana == '2') Mandiri @else Hibah @endif</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <h3>Rencana Anggaran</h3>
            
            <table class="table table-bordered">
                <thead class="table_thead">
                    <tr>
                        <th class="tabel_th" width="2%">No</th>
                        <th class="tabel_th" width="20%">Item</th>
                        <th class="tabel_th" width="8%">Biaya Satuan</th>
                        <th class="tabel_th" width="3%">Total Qty</th>
                        <th class="tabel_th" width="3%">Frekuensi</th>
                        <th class="tabel_th" width="12%">Total</th>
                        <th class="tabel_th" width="5%">Sumber</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($anggarans as $no => $anggaran)
                    <tr>
                        <td style="text-align: center;" class="table_td">{{++$no}}</td>
                        <td class="table_td">{{$anggaran->item}}</td>
                        <td style="text-align: right;" class="table_td">{{currency_IDR($anggaran->biaya_satuan)}}</td>
                        <td style="text-align: center;" class="table_td">{{$anggaran->quantity}}</td>
                        <td style="text-align: center;" class="table_td">{{$anggaran->frequency}}</td>
                        @php $total = 0; $total = $anggaran->biaya_satuan * $anggaran->quantity * $anggaran->frequency; @endphp
                        <td style="text-align: right;" class="table_td">{{currency_IDR($total)}}</td>
                        <td style="text-align: center;" class="table_td">@if($anggaran->sumber_dana == '1') Kampus @elseif($anggaran->sumber_dana == '2') Mandiri @else Hibah @endif</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="5" style="text-align: right; font-style:bold;" class="table_td">Grand Total</td>
                        <td style="text-align: right; font-style:bold;" class="table_td">{{currency_IDR($grandTotal['grandTotal'])}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <h3>Penutup</h3>
            @foreach($datas as $penutup)
            <p style="text-align: justify;">{!!$penutup->penutup!!}</p>
            <p style="margin-top: 2em;">Batam, {{date('d-m-Y', strtotime($penutup->created_at))}}</p>
            @endforeach

            <table class="footer_auditor" width="100%">
                @if($getQR->count() > 0)
                @foreach($getQR as $qr) 
                <tr class="trfooterauditor">
                        <td class="tdfooterauditor" colspan="10">Disusun oleh,<br><br>
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($qr->generate_qrcode)) !!}">
                            <br><br><b>@foreach($datas as $nn) {{$nn->nama_user_dosen}}{{$nn->nama_user_mahasiswa}} @endforeach</b><br><i>@foreach($datas as $nama_fk) {{$nama_fk->nama_prodi}} @endforeach</i>
                        </td>
                        <td class="tdfooterauditor" colspan="10">Diketahui oleh,<br><br> 
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($qr->generate_qrcode)) !!}">
                            <br><br><b>@foreach($getDekan as $dekan) {{$dekan->name}} @endforeach</b><br><i>Dekan</i>
                        </td>
                        <td class="tdfooterauditor">Disetujui oleh,<br><br> 
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($qr->generate_qrcode)) !!}">
                            <br><br> @foreach($datas as $jenpro) @if($jenpro->id_jenis_kegiatan == 1) <b>Benny Roesly, S.T., M.Pd.</b><br><i>WRSDP</i> @else <b>Yodi, S.Kom., M.S.I</b><br><i>WRAK</i> @endif @endforeach
                        </td>
                </tr> 
                @endforeach
                @else
                <tr class="trfooterauditor">
                    <td class="tdfooterauditor" colspan="10">Disusun oleh,<br><br>
                        <p style="margin-top: 2em;"></p>
                        <br><br><b>@foreach($datas as $nn) {{$nn->nama_user_dosen}}{{$nn->nama_user_mahasiswa}} @endforeach</b><br><i>@foreach($datas as $nama_fk) {{$nama_fk->nama_prodi}} @endforeach</i>
                    </td>
                    <td class="tdfooterauditor" colspan="10">Diketahui oleh,<br><br> 
                        <p style="margin-top: 2em;"></p>
                        <br><br><b>@foreach($getDekan as $dekan) {{$dekan->name}} @endforeach</b><br><i>Dekan</i>
                    </td>
                    <td class="tdfooterauditor">Disetujui oleh,<br><br> 
                        <p style="margin-top: 2em;"></p>
                        <br><br> @foreach($datas as $jenpro) @if($jenpro->id_jenis_kegiatan == 1) <b>Benny Roesly, S.T., M.Pd.</b><br><i>WRSDP</i> @else <b>Yodi, S.Kom., M.S.I</b><br><i>WRAK</i> @endif @endforeach
                    </td>
                </tr>
                @endif
            </table>
            
        </div>
        {{-- <div class="page-break-lampiran">
            @if($data_lampiran->count() > 0)
            <h3 class="mb-3">Lampiran</h3>
                @foreach($data_lampiran as $lampirans)
                    <img src="{{ public_path(''.$lampirans->berkas.'') }}" alt="Default Image" style="max-width: 500px; max-height: 500px; text-align:center;" class="mb-3">
                    <p class="mb-2"><i>{{$lampirans->nama_berkas}}</i></p>
                @endforeach
            @else
                <p></p>
            @endif
        </div>
    </div> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>