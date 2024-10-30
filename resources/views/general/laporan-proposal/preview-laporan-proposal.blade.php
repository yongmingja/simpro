<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Preview Laporan Proposal</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    @page {
        margin-top: 180px;
    }
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
    .cover_instansi {
        line-height: 8px;
        margin-top: 10em;
        font-style: bold;
    }
    .content_proposal {
        margin-left: 1em;
    }
    header {
        margin-left: 1em;
        margin-right: 0em;
        position: fixed;
        width: 98%;
        margin-top: -130px;
    }
    .justify-text {
        text-align: justify;
    }
</style>
<body>
    <header>
        <table class="table table-bordered">
            <tr>
                <td style="vertical-align: middle; text-align:center; width: 24%;"><img src="{{public_path('assets/img/logo-uvers-blue.jpg')}}" alt="logo-uvers-blue" style="height:60px;"></td>
                <td colspan="3" style="vertical-align: middle; text-align:center; font-style: bold;">LAPORAN PERTANGGUNGJAWABAN KEGIATAN AKADEMIK</td>
            </tr>
            <tr>
                <td style="text-align: center;">No. Dokumen</td>
                <td style="text-align: center;">Revisi</td>
                <td style="text-align: center;">Tanggal</td>
                <td style="text-align: center;">Halaman</td>
            </tr>
            <tr>
                <td style="text-align: center;">F-M2.STD-PD-8.3</td>
                <td style="text-align: center;">2</td>
                <td style="text-align: center;">21 Juli 2023</td>
                <td style="text-align: center;"></td>
            </tr>
        </table>
    </header>     
    <div class="page-break">
        <p style="text-transform: uppercase; margin-top: 1em; font-style: bold; font-size:14px;">LAPORAN PERTANGGUNGJAWABAN KEGIATAN</p>
        @foreach($datas as $kegiatan)
        <h3 style="text-transform: uppercase;">{{$kegiatan->nama_kegiatan}}</h3>
        <h3 style="text-transform: uppercase; font-size: 14px;">{{$kegiatan->lokasi_tempat}}</h3>
        <p>{{tanggal_indonesia($kegiatan->tgl_event)}}</p>
        @endforeach
        <img src="{{public_path('assets/img/logo-uvers.png')}}" alt="logo-uvers" style="margin-top: 6em;">
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
    <div class="main-table">
        <div class="content_proposal">
            <div class="justify-text">
                <h3>Pendahuluan</h3>
                @foreach($datas as $pendahuluan)
                    <p>{!!$pendahuluan->pendahuluan!!}</p>
                @endforeach

                <h3>Tujuan dan Manfaat</h3>
                @foreach($datas as $tujuan)
                    <p>{!!$tujuan->tujuan_manfaat!!}</p>
                @endforeach

                <h3>Peserta</h3>
                @foreach($datas as $peserta)
                    <p>{!!$peserta->peserta!!}</p>
                @endforeach

                <h3>Detil Kegiatan</h3>
                @foreach($datas as $detil)
                    <p>{!!$detil->detil_kegiatan!!}</p>
                @endforeach

                <h3>Hasil Kegiatan</h3>
                @foreach($datas as $hasil)
                    <p>{!!$hasil->hasil_kegiatan!!}</p>
                @endforeach

                <h3>Evaluasi dan Catatan Kegiatan</h3>
                @foreach($datas as $evaluasi)
                    <p>{!!$evaluasi->evaluasi_catatan_kegiatan!!}</p>
                @endforeach
            </div>

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
                        <td colspan="5" style="text-align: right; font-style:bold;" class="table_td">Total Biaya</td>
                        <td style="text-align: right; font-style:bold;" class="table_td">{{currency_IDR($grandTotalAnggarans['grandTotal'])}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <h3>Realisasi Anggaran</h3>
            
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
                    @foreach($realisasianggarans as $no => $realisasi)
                    <tr>
                        <td style="text-align: center;" class="table_td">{{++$no}}</td>
                        <td class="table_td">{{$realisasi->item}}</td>
                        <td style="text-align: right;" class="table_td">{{currency_IDR($realisasi->biaya_satuan)}}</td>
                        <td style="text-align: center;" class="table_td">{{$realisasi->quantity}}</td>
                        <td style="text-align: center;" class="table_td">{{$realisasi->frequency}}</td>
                        @php $total = 0; $total = $realisasi->biaya_satuan * $realisasi->quantity * $realisasi->frequency; @endphp
                        <td style="text-align: right;" class="table_td">{{currency_IDR($total)}}</td>
                        <td style="text-align: center;" class="table_td">@if($realisasi->sumber_dana == '1') Kampus @elseif($realisasi->sumber_dana == '2') Mandiri @else Hibah @endif</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="5" style="text-align: right; font-style:bold;" class="table_td">Total Biaya</td>
                        <td style="text-align: right; font-style:bold;" class="table_td">{{currency_IDR($grandTotalRealisasiAnggarans['grandTotalRealisasi'])}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <h3>Penutup</h3>
            @foreach($datas as $penutup)
            <div class="justify-text">
                <p>{!!$penutup->lap_penutup!!}</p>
            </div>
            <p style="margin-top: 2em;">Batam, {{date('d-m-Y', strtotime($penutup->tgl_laporan))}}</p>
            @endforeach

            <table class="footer_auditor" width="100%">
                @foreach($getQR as $qr) @endforeach  
                <tr class="trfooterauditor">
                    @if($qr->status_laporan == 0)
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
                    @else
                        <td class="tdfooterauditor" colspan="10">Disusun oleh,<br><br>
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($qr->qrcode)) !!}">
                            <br><br><b>@foreach($datas as $nn) {{$nn->nama_user_dosen}}{{$nn->nama_user_mahasiswa}} @endforeach</b><br><i>@foreach($datas as $nama_fk) {{$nama_fk->nama_prodi}} @endforeach</i>
                        </td>
                        <td class="tdfooterauditor" colspan="10">Diketahui oleh,<br><br> 
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($qr->qrcode)) !!}">
                            <br><br><b>@foreach($getDekan as $dekan) {{$dekan->name}} @endforeach</b><br><i>Dekan</i>
                        </td>
                        <td class="tdfooterauditor">Disetujui oleh,<br><br> 
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($qr->qrcode)) !!}">
                            <br><br> @foreach($datas as $jenpro) @if($jenpro->id_jenis_kegiatan == 1) <b>Benny Roesly, S.T., M.Pd.</b><br><i>WRSDP</i> @else <b>Yodi, S.Kom., M.S.I</b><br><i>WRAK</i> @endif @endforeach
                        </td>
                    @endif  
                </tr>                                       
            </table>
            
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script type="text/php">        
        if (isset($pdf)) {
            $x = 500;
            $y = 103;
            $text = "{PAGE_NUM} / {PAGE_COUNT}";
            $font = "Times New Roman";
            $size = 11;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>
</body>
</html>