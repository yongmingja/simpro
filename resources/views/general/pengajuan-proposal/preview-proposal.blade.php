<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Preview Proposal</title>
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
    .tag-kode {
        text-align: left;
        font-size: 12px;
        line-height: 14px;
        margin-left: 1em;
        margin-top: 3em;
    }
</style>
<body>
        <header>
            <table class="table table-bordered">
                <tr>
                    <td style="vertical-align: middle; text-align:center; width: 24%;"><img src="{{public_path('assets/img/logo-uvers-blue.jpg')}}" alt="logo-uvers-blue" style="height:60px;"></td>
                    <td colspan="3" style="vertical-align: middle; text-align:center; font-style: bold;">PROPOSAL KEGIATAN AKADEMIK</td>
                </tr>
                <tr>
                    <td style="text-align: center;">No. Dokumen</td>
                    <td style="text-align: center;">Revisi</td>
                    <td style="text-align: center;">Tanggal</td>
                    <td style="text-align: center;">Halaman</td>
                </tr>
                <tr>
                    <td style="text-align: center;">F-M2.STD-PD-8.2</td>
                    <td style="text-align: center;">2</td>
                    <td style="text-align: center;">21 Juli 2023</td>
                    <td style="text-align: center;"></td>
                </tr>
            </table>

        </header>      
        <div class="page-break">
            <p style="text-transform: uppercase; margin-top: 1em; font-style: bold; font-size:14px;">PROPOSAL KEGIATAN</p>
            @foreach($datas as $kegiatan)
            <h3 style="text-transform: uppercase;">{{$kegiatan->nama_kegiatan}}</h3>
            <h3 style="text-transform: uppercase; font-size:12px;">{{$kegiatan->lokasi_tempat}}</h3>
            <p>{{tanggal_indonesia($kegiatan->tgl_event)}}</p>
            @endforeach
            <img src="{{public_path('assets/img/logo-uvers.png')}}" alt="logo-uvers" style="margin-top: 6em;">
            <p style="margin-top: 3em;">Pengusul:</p>
            
            <p style="text-transform: uppercase;"><b> @foreach($datas as $pengusul) {{$pengusul->nama_user_dosen}}{{$pengusul->nama_user_mahasiswa}} @endforeach &ndash; @foreach($datas as $userid) {{$userid->user_id}} @endforeach</b></p>
            
            <div class="cover_instansi">
                @foreach($datas as $nama)
                <p style="text-transform: uppercase;">{{$nama->nama_prodi_biro}}</p>
                <p style="text-transform: uppercase;">{{$nama->nama_fakultas_biro}}</p>
                @endforeach
                <p style="text-transform: uppercase;">Universitas Universal</p>
                @foreach($datas as $tahun)
                <p style="text-transform: uppercase;">{{date('Y', strtotime($tahun->tgl_event))}}</p>
                @endforeach
            </div>

            <div class="tag-kode">
                <p>KODE RENSTRA:<br>Kode AKUN:</p>
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
                </div>
                @if($sarpras->count() > 0 )

                <h3>Keperluan Sarana Prasarana</h3>
                
                <div>
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
                </div>
                @endif

                @if($anggarans->count() > 0)
                <h3>Rencana Anggaran</h3>
                
                <div>
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
                </div>
                @endif

                <h3>Penutup</h3>
                @foreach($datas as $penutup)
                <div class="justify-text">
                    <p>{!!$penutup->penutup!!}</p>
                </div>
                <p style="margin-top: 2em;">Batam, {{tanggal_indonesia($penutup->created_at)}}</p>
                @endforeach

                <div>
                    <table class="footer_auditor" width="100%">
                        @if($getQR->count() > 0)
                            @foreach($getQR as $qr) 
                                <tr class="trfooterauditor">
                                        @if($getPengusul != null)
                                            <td class="tdfooterauditor" colspan="10">Disusun oleh,<br><br>
                                                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($qr->generate_qrcode)) !!}">
                                                <br><br><b>{{$getPengusul->nama_pegawai}}</b><br><i>@if($getPengusul->ket_jabatan != '') {{$getPengusul->ket_jabatan}} @else {{$getPengusul->nama_jabatan}} @endif</i>
                                            </td>
                                        @endif
                                        @if($getDiketahui != null)
                                        <td class="tdfooterauditor" colspan="10">Diketahui oleh,<br><br> 
                                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($qr->generate_qrcode)) !!}">
                                            <br><br><b>{{$getDiketahui->nama_pegawai}}</b><br><i>@if($getDiketahui->ket_jabatan != '') {{$getDiketahui->ket_jabatan}} @else {{$getDiketahui->nama_jabatan}} @endif</i>
                                        </td>
                                        @endif
                                        @if($getDisetujui != null)
                                        <td class="tdfooterauditor">Disetujui oleh,<br><br> 
                                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($qr->generate_qrcode)) !!}">
                                            <br><br><b>{{$getDisetujui->nama_pegawai}}</b><br><i>{{$getDisetujui->kode_jabatan}}</i>
                                        </td>
                                        @endif
                                </tr> 
                            @endforeach
                        @else
                            <tr class="trfooterauditor">
                                @if($getPengusul != null)
                                    <td class="tdfooterauditor" colspan="10">Disusun oleh,<br><br>
                                        <p style="margin-top: 2em;"></p>
                                        <br><br><b>{{$getPengusul->nama_pegawai}}</b><br><i>@if($getPengusul->ket_jabatan != '') {{$getPengusul->ket_jabatan}} @else {{$getPengusul->nama_jabatan}} @endif</i>
                                    </td>
                                @endif
                                @if($getDiketahui != null)
                                <td class="tdfooterauditor" colspan="10">Diketahui oleh,<br><br> 
                                    <p style="margin-top: 2em;"></p>
                                    <br><br><b>{{$getDiketahui->nama_pegawai}}</b><br><i>@if($getDiketahui->ket_jabatan != '') {{$getDiketahui->ket_jabatan}} @else {{$getDiketahui->nama_jabatan}} @endif</i>
                                </td>
                                @endif 
                                @if($getDisetujui != null)
                                <td class="tdfooterauditor">Disetujui oleh,<br><br> 
                                    <p style="margin-top: 2em;"></p>
                                    <br><br><b>{{$getDisetujui->nama_pegawai}}</b><br><i>{{$getDisetujui->kode_jabatan}}</i>
                                </td>
                                @endif 
                            </tr>
                        @endif
                    </table>
                </div>
                
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