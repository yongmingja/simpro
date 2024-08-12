<?php

    function set_active($route)
    {
        if(Route::is($route))
        {
            return 'active';
        }
    }

    function tanggal_indonesia($tgl, $tampil_hari=false){
        $nama_hari=array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");
        $nama_bulan = array (
                1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
                "September", "Oktober", "November", "Desember");
        $tahun=substr($tgl,0,4);
        $bulan=$nama_bulan[(int)substr($tgl,5,2)];
        $tanggal=substr($tgl,8,2);
        $text="";
        if ($tampil_hari) {
            $urutan_hari=date('w', mktime(0,0,0, substr($tgl,5,2), $tanggal, $tahun));
            $hari=$nama_hari[$urutan_hari];
            $text .= $hari.", ";
        }
            $text .=$tanggal ." ". $bulan ." ". $tahun;
        return $text;
    }

    function hariIndo ($hariInggris) {
        switch ($hariInggris) {
          case 'Sunday':
            return 'Minggu';
          case 'Monday':
            return 'Senin';
          case 'Tuesday':
            return 'Selasa';
          case 'Wednesday':
            return 'Rabu';
          case 'Thursday':
            return 'Kamis';
          case 'Friday':
            return 'Jumat';
          case 'Saturday':
            return 'Sabtu';
          default:
            return 'hari tidak valid';
        }
    }

    if(!function_exists('currency_IDR'))
    {
        function currency_IDR($value)
        {
            return "Rp" .number_format($value,0,',','.');
        }
    }