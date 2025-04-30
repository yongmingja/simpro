@extends('layouts.backend')
@section('title','Ajukan Proposal')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="javascript:void(0)">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('tampilan-proposal-baru')}}">@yield('title')</a>
      </li>
      <li class="breadcrumb-item active">Data</li>
    </ol>
</nav>
</div>
@endsection
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/typography.css')}}" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />
@section('content')
<style>
    body.no-scroll {
        overflow: hidden; /* Menghentikan scroll */
    }
    #wizard-container {
        max-height: 70vh; /* Batasi tinggi wizard */
        overflow-y: auto;
        padding: 0.8rem; 
        background-color: rgba(255, 255, 255, 0.03); 
        backdrop-filter: blur(px); 
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); 
    }

    .bs-stepper-header {
        display: flex;
        gap: 0.5rem; /* Tambahkan jarak antar-tab */
        width: 100%; /* Pastikan ukuran penuh dalam container */
        max-width: 100%; /* Tab tetap berada di card */
        overflow: hidden;
    }

    @media (max-width: 1024px) {
        .bs-stepper-header {
            display: flex;
            gap: 0.5rem; /* Tambahkan jarak antar-tab */
            width: 100%; /* Pastikan ukuran penuh dalam container */
            max-width: 100%; /* Tab tetap berada di card */
            overflow: hidden;
        }
    }

    @media (max-width: 768px) {
        #wizard-container {
            max-height: 30vh; 
            overflow-y: auto;
            padding: 0.8rem; 
        }
    }

</style>
<div class="container-fluid flex-grow-1">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">                
                <!-- MULAI TOMBOL TAMBAH -->
                <div class="mb-3">
                    <a href="{{route('submission-of-proposal.index')}}"><button type="button" class="btn btn-outline-secondary"><i class="bx bx-chevron-left"></i>Back</button></a> 
                </div>  

                <input type="hidden" name="recentRole" id="recentRole">
                <input type="hidden" name="catchIDFakultasBiro" id="catchIDFakultasBiro">
                <!-- AKHIR TOMBOL -->
                <div class="bs-stepper wizard-vertical horizontal">
                    <div class="bs-stepper-header mt-3">
                        <div class="step" data-target="#page-1">
                            <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">1</span>
                            <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Informasi Utama</span>
                                <span class="bs-stepper-subtitle">Isi Informasi Utama</span>
                            </span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#page-2">
                            <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">2</span>
                            <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Sarana Prasarana</span>
                                <span class="bs-stepper-subtitle">Isi Sarana Prasarana</span>
                            </span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#page-3">
                            <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">3</span>
                            <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Rencana Anggaran</span>
                                <span class="bs-stepper-subtitle">Isi Rencana Anggaran</span>
                            </span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#page-4">
                            <button type="button" class="step-trigger" id="tombol-page-4">
                            <span class="bs-stepper-circle">4</span>
                            <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Lampiran <i>(Opsional)</i></span>
                                <span class="bs-stepper-subtitle">Upload Lampiran</span>
                            </span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#page-5">
                            <button type="button" class="step-trigger" id="tombol-page-5">
                            <span class="bs-stepper-circle">5</span>
                            <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Penutup</span>
                                <span class="bs-stepper-subtitle">Isi Penutup</span>
                            </span>
                            </button>
                        </div>
                    </div>

                    <div class="bs-stepper-content" id="wizard-container">
                        <form onSubmit="return false" id="form-proposal">

                        <!-- Informasi Utama -->
                        <div class="container">
                            <div id="page-1" class="content">
                                <div class="row g-3">
                                   
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <label class="form-label" for="id_jenis_kegiatan">Kategori Proposal</label>
                                                <select class="select2 form-control" id="id_jenis_kegiatan" name="id_jenis_kegiatan" aria-label="Default select example" style="cursor:pointer;" onchange="getFormRkat()">
                                                    <option value="" id="choose_jenis_kegiatan" readonly>- Pilih -</option>
                                                    @forEach($getJenisKegiatan as $data)
                                                    <option value="{{$data->id}}" data-name="{{$data->nama_jenis_kegiatan}}">{{$data->nama_jenis_kegiatan}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="kategoriErrorMsg" style="font-size: 10px;"></span>
                                            </div>
                                            <div id="showForm" name="showForm" class="col-sm-8 d-none"></div>
                                        </div>                                        
                                    </div>

                                    <div class="col-md-6">
                                        <label for="tgl_event" class="form-label">Tanggal Kegiatan</label>
                                        <input type="date" class="form-control" id="tgl_event" name="tgl_event" value="" placeholder="mm/dd/yyyy" />
                                        <span class="text-danger" id="tglKegiatanErrorMsg" style="font-size: 10px;"></span>
                                    </div>

                                     <div class="col-md-6">
                                        <label class="form-label" for="id_fakultas_biro">Fakultas atau Unit</label>
                                        <select class="select2 form-control border border-primary" id="id_fakultas_biro" name="id_fakultas_biro" aria-label="Default select example" style="cursor:pointer;">
                                            <option value="" id="choose_faculty" readonly>- Select faculty or unit -</option>
                                            @foreach($getFakultasBiro as $facultyBiro)
                                                <option value="{{$facultyBiro->id}}">{{$facultyBiro->nama_fakultas_biro}}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger" id="fakultasBiroErrorMsg" style="font-size: 10px;"></span>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="id_prodi_biro">Prodi atau Biro</label>
                                        <select class="select2 form-control border border-primary" id="id_prodi_biro" name="id_prodi_biro" aria-label="Default select example" style="cursor:pointer;">
                                            <option value="" class="d-none">- Pilih prodi -</option>
                                        </select>
                                        <span class="text-danger" id="prodiBiroErrorMsg" style="font-size: 10px;"></span>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                                        <input type="text" id="nama_kegiatan" name="nama_kegiatan" class="form-control">
                                        <span class="text-danger" id="namaKegiatanErrorMsg" style="font-size: 10px;"></span>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="lokasi_tempat" class="form-label">Tempat atau Lokasi Kegiatan</label>
                                        <input type="text" id="lokasi_tempat" name="lokasi_tempat" class="form-control">
                                        <span class="text-danger" id="lokasiErrorMsg" style="font-size: 10px;"></span>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="pendahuluan" class="form-label">Pendahuluan</label>
                                        <div id="editor-pendahuluan" class="mb-3" style="height: 300px;"></div>
                                        <textarea rows="3" class="mb-3 d-none" name="pendahuluan" id="pendahuluan"></textarea>
                                        <span class="text-danger" id="pendahuluanErrorMsg" style="font-size: 10px;"></span>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="tujuan_manfaat" class="form-label">Tujuan dan Manfaat</label>
                                        <div id="editor-tujuan-manfaat" class="mb-3" style="height: 300px;"></div>
                                        <textarea id="tujuan_manfaat" class="mb-3 d-none" name="tujuan_manfaat" rows="3"></textarea>
                                        <span class="text-danger" id="tujuanManfaatErrorMsg" style="font-size: 10px;"></span>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="peserta" class="form-label">Peserta</label>
                                        <div id="editor-peserta" class="mb-3" style="height: 300px;"></div>
                                        <textarea class="mb-3 d-none" id="peserta" name="peserta" rows="5"></textarea>
                                        <span class="text-danger" id="pesertaErrorMsg" style="font-size: 10px;"></span>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="detil_kegiatan" class="form-label">Detil Kegiatan</label>
                                        <div id="editor-detil-kegiatan" class="mb-3" style="height: 300px;"></div>
                                        <textarea class="mb-3 d-none" id="detil_kegiatan" name="detil_kegiatan" rows="5"></textarea>
                                        <span class="text-danger" id="detilKegiatanErrorMsg" style="font-size: 10px;"></span>
                                    </div>

                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev" disabled> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="bx bx-chevron-right bx-sm me-sm-n2"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sarana Prasarana -->
                        <div class="container">
                            <div id="page-2" class="content">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <table class="table table-borderless" id="dynamicAddRemove">
                                            <thead>
                                                <tr>
                                                    <th>Tgl. Kegiatan</th>
                                                    <th>Sarpras</th>
                                                    <th width="12%">Jumlah</th>
                                                    <th>Sumber Dana</th>
                                                    <th>Ket.</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input type="date" class="form-control" name="kolom[0][tgl_kegiatan]" placeholder="mm/dd/yyyy" /></td>
                                                    <td><input type="text" class="form-control" name="kolom[0][sarpras_item]" /></td>
                                                    <td><input type="number" class="form-control" name="kolom[0][jumlah]" min="0" /></td>
                                                    <td>
                                                        <select class="select2 form-select" name="kolom[0][sumber_sarpras]">
                                                            <option value="1">Kampus</option>
                                                            <option value="2">Mandiri</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="kolom[0][ket]" /></td>
                                                    <td><button type="button" class="btn btn-warning" id="tombol-add-sarpras"><i class="bx bx-plus-circle"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>                                        
                                    </div>
                                    <p style="font-size: 14px;" class="text-info">**Silakan klik next jika tidak ada sarana prasarana yang dibutuhkan</p>
                                    <div class="col-12 d-flex justify-content-between">
                                    <button class="btn btn-primary btn-prev"> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                    </button>
                                    <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="bx bx-chevron-right bx-sm me-sm-n2"></i></button>
                                    </div>
                                </div>
                                </div>
                        </div>

                        <!-- Rencana Anggaran -->
                        <div class="container">
                            <div id="page-3" class="content">
                                <div class="content-header mb-3">
                                    <div class="mt-2 g-3">
                                    <h4 id="tampilkan-total" style="color: aqua;"></h4>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <table class="table table-borderless" id="dynamicAddRemoveAnggaran">
                                    
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Biaya Satuan</th>
                                            <th width="12%;">Qty</th>
                                            <th width="12%;">Freq.</th>
                                            <th>Total Biaya</th>
                                            <th>Sumber</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody id="table-body">
                                        <tr>
                                            <td><input type="text" class="form-control" id="item" name="rows[0][item]" value="" placeholder="Input nama item" /></td>
                                            <td><input type="number" class="form-control biaya_satuan" id="biaya_satuan" name="rows[0][biaya_satuan]" value="" min="0" onkeyup="OnChange(this)" /></td>
                                            <td><input type="number" class="form-control quantity" id="quantity" name="rows[0][quantity]" value="" min="0" onkeyup="OnChange(this)" /></td>
                                            <td><input type="number" class="form-control frequency" id="frequency" name="rows[0][frequency]" value="" min="0" onkeyup="OnChange(this)" /></td>
                                            <td><input type="text" class="form-control total_biaya" id="total_biaya" name="rows[0][total_biaya]" value="" min="0" readonly style="cursor: no-drop;" /></td>
                                            <td><select class="select2 form-select" id="sumber_anggaran" name="rows[0][sumber_anggaran]" onchange="calculateGrandTotal()" style="cursor:pointer;">
                                                <option value="" id="pilih_sumber_anggaran">- Pilih -</option>
                                                <option value="1">Kampus</option>
                                                <option value="2">Mandiri</option>
                                            </select></td>
                                            <td><button type="button" class="btn btn-warning btn-block" id="tombol-add-anggaran"><i class="bx bx-plus-circle"></i></button></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" style="font-weight: bold; text-align:right;">Grand Total</td>
                                            <td colspan="3" style="font-weight: bold; text-align: left;" id="grand-total"></td>
                                        </tr>
                                    </tfoot>                                        
                                </table>
                                
                                    <p style="font-size: 14px;" class="text-info">**Silakan klik next jika tidak memiliki rencana anggaran pada proposal kegiatan</p>
                                    <div class="col-12 d-flex justify-content-between">
                                    <button class="btn btn-primary btn-prev"> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                    </button>
                                    <button class="btn btn-primary btn-next" id="next-anggaran"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="bx bx-chevron-right bx-sm me-sm-n2"></i></button>
                                    </div>
                                </div>
                                </div>
                        </div>

                        <!-- Lampiran Proposal -->
                        <div class="container">
                            <div id="page-4" class="content">
                                <div class="col form-group p-0">
                                        <p style="font-size: 12px; line-height:14px;" class="text-primary mt-1">Note:<br>- Anda bisa menggunakan link google drive atau unggah berkas.<br>- Jika ingin unggah berupa berkas, silakan checklist upload berkas.<br></p>
                                </div>
                                <input type="checkbox" id="switch" name="switch" class="mb-3"> Upload Berkas
                                <div class="row g-3">                                    
                                    <div>
                                        <div class="row firstRow">
                                            <div class="col-md-3 form-group mb-3">
                                                <label for="nama_berkas" class="form-label">Nama Berkas</label>
                                                <input type="text" name="nama_berkas[]" class="w-100 form-control">
                                            </div>
                                            
                                            <div class="col-md-3 form-group mb-3" id="form-container">
                                                <!-- Form content will be dynamically generated here --> 
                                            </div>
                                            <div class="col-md-3 form-group mb-3">
                                                <label for="keterangan" class="form-label">Keterangan</label>
                                                <input type="text" name="keterangan[]" class="w-100 form-control">
                                            </div>
                                            <div class="col-md-3 form-group mb-3">
                                                <button class="btn btn-warning addField mt-4" id="tombol"><i class="bx bx-plus-circle bx-xs"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <p style="font-size: 14px;" class="text-info">**Silakan klik next jika tidak ada data atau berkas yang ingin dilampirkan</p>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-primary btn-prev"> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="bx bx-chevron-right bx-sm me-sm-n2"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Penutup -->
                        <div class="container">
                            <div id="page-5" class="content">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="penutup" class="form-label">Penutup</label>
                                        <div id="editor-penutup" class="mb-3" style="height: 300px;"></div>
                                        <textarea class="mb-3 d-none" id="penutup" name="penutup" rows="5"></textarea>
                                        <span class="text-danger" id="penutupErrorMsg" style="font-size: 10px;"></span>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-primary btn-prev"> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-success btn-submit" id="tombol-simpan">Submit</button>
                                    </div>
                                    
                                </div>
                            </div> 
                        </div>                             

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@section('script')
    <script>
      const quill = new Quill('#editor', {
        theme: 'snow'
      });
    </script>

<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        const recentPeranUrl = "{{ route('get-recent-peran') }}";
        
        // Panggil API untuk mendapatkan recentPeranIs
        fetch(recentPeranUrl)
            .then(response => response.json())
            .then(data => {
                const recentPeranIs = data.recentPeranIs;
                const unitIs = data.unitIs;
                $('#recentRole').val(recentPeranIs);
                $('#catchIDFakultasBiro').val(unitIs); // Throw nilai ini ke inputan untuk kemudian di ambil saat kategori RKAT

                // Ambil elemen form
                const fakultasBiro = document.getElementById("id_fakultas_biro");
                const prodiBiro = document.getElementById("id_prodi_biro");

                // Cek apakah recentPeranIs bernilai "WAREK"
                if (recentPeranIs === "WAREK") {
                    // Nonaktifkan elemen form
                    fakultasBiro.disabled = true;
                    prodiBiro.disabled = true;

                    // Tambahkan gaya opsional
                    fakultasBiro.style.backgroundColor = "#e9ecef";
                    prodiBiro.style.backgroundColor = "#e9ecef";
                }
            })
            .catch(error => console.error('Error fetching recentPeranIs:', error));
            
    });

    document.addEventListener("DOMContentLoaded", function () {
        const body = document.body;
        const wizardContainer = document.getElementById("wizard-container");

        // Saat wizard diaktifkan, tambahkan kelas no-scroll
        if (wizardContainer) {
            body.classList.add("no-scroll");
        }
    });

    // Untuk mendeteksi selisih hari, tidak boleh < 14 hari
    document.getElementById("tgl_event").addEventListener("change", function() {
        const selectedDate = new Date(this.value);
        const today = new Date();
        const diffTime = selectedDate - today;
        const diffDays = diffTime / (1000 * 60 * 60 * 24); 

        if (diffDays < 14) {
            alert("Peringatan! Pengajuan proposal minimal 14 hari sebelum tanggal kegiatan.");
        }
    });

    const wizardVertical = document.querySelector(".wizard-vertical");

    if (typeof wizardVertical !== undefined && wizardVertical !== null) {
        const wizardVerticalBtnNextList = [].slice.call(wizardVertical.querySelectorAll('.btn-next')),
            wizardVerticalBtnPrevList = [].slice.call(wizardVertical.querySelectorAll('.btn-prev')),
            wizardVerticalBtnSubmit = wizardVertical.querySelector('.btn-submit');

        const numberedVerticalStepper = new Stepper(wizardVertical, {
            linear: false
        });
        if (wizardVerticalBtnNextList) {
            wizardVerticalBtnNextList.forEach(wizardVerticalBtnNext => {
                wizardVerticalBtnNext.addEventListener('click', event => {
                    numberedVerticalStepper.next();
                });
            });
        }
        if (wizardVerticalBtnPrevList) {
            wizardVerticalBtnPrevList.forEach(wizardVerticalBtnPrev => {
                wizardVerticalBtnPrev.addEventListener('click', event => {
                    numberedVerticalStepper.previous();
                });
            });
        }

        if (wizardVerticalBtnSubmit) {
            wizardVerticalBtnSubmit.addEventListener('click', event => {
                // Validasi semua input file yang dinamis
                const fileInputs = document.querySelectorAll('input[name^="berkas"]'); // Pilih semua input file dengan nama "berkas[...]"
                const maxSize = 2 * 1024 * 1024; // Maksimal ukuran file 2MB
                let isValid = true; // Flag validasi untuk semua file

                fileInputs.forEach((fileInput, index) => {
                    const errorMsgId = `berkasErrorMsg_${index}`; // ID unik untuk setiap pesan error
                    let errorMsg = document.getElementById(errorMsgId);

                    // Jika elemen pesan error tidak ada, buat elemen baru
                    if (!errorMsg) {
                        errorMsg = document.createElement('span');
                        errorMsg.id = errorMsgId;
                        errorMsg.className = 'text-danger';
                        errorMsg.style.fontSize = '10px';
                        fileInput.parentNode.appendChild(errorMsg); // Tambahkan ke DOM
                    }

                    const files = fileInput.files;
                    if (files.length > 0) {
                        const file = files[0]; // Ambil file pertama di input
                        if (file.size > maxSize) {
                            errorMsg.innerHTML = 'Ukuran berkas tidak boleh melebihi 2MB.';
                            fileInput.value = ''; // Reset input file
                            Swal.fire({
                                title: 'Error!',
                                text: `Terdapat ukuran berkas lebih dari 2MB. Silakan unggah file dengan ukuran maksimal 2MB.`,
                                icon: 'error',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false,
                            });
                            isValid = false; // Tandai validasi gagal
                        } else {
                            errorMsg.innerHTML = ''; // Hapus pesan error jika valid
                        }
                    }
                });

                if (!isValid) {
                    return; // Hentikan proses submit jika ada file yang tidak valid
                }

                // Melanjutkan validasi form jika semua file valid
                if ($("#form-proposal").length > 0) {
                    $("#form-proposal").validate({
                        submitHandler: function (form) {
                            var actionType = $('#tombol-simpan').val();
                            var formData = new FormData($("#form-proposal")[0]);
                            $('#tombol-simpan').html('');
                            $('#tombol-simpan').prop("disabled", true);

                            $.ajax({
                                data: formData,
                                contentType: false,
                                processData: false,
                                url: "{{ route('insert-proposal') }}",
                                type: "POST",
                                dataType: 'json',
                                beforeSend: function () {
                                    $("#tombol-simpan").append(
                                        '<i class="bx bx-loader-circle bx-spin text-warning"></i>' +
                                        ' Mohon tunggu ...');
                                },
                                success: function (data) {
                                    $('#form-proposal').trigger("reset");
                                    $('#tombol-simpan').html('Submit');
                                    $('#tombol-simpan').prop("disabled", true);
                                    Swal.fire({
                                        title: 'Good job!',
                                        text: 'Proposal submitted successfully!',
                                        type: 'success',
                                        customClass: {
                                            confirmButton: 'btn btn-primary'
                                        },
                                        buttonsStyling: false,
                                        timer: 2000
                                    }),
                                    window.location = '{{ route("submission-of-proposal.index") }}';
                                },
                                error: function (response) {
                                    $('#kategoriErrorMsg').text(response.responseJSON.errors.id_jenis_kegiatan);
                                    $('#tglKegiatanErrorMsg').text(response.responseJSON.errors.tgl_event);
                                    $('#fakultasBiroErrorMsg').text(response.responseJSON.errors.id_fakultas_biro);
                                    $('#prodiBiroErrorMsg').text(response.responseJSON.errors.id_prodi_biro);
                                    $('#namaKegiatanErrorMsg').text(response.responseJSON.errors.nama_kegiatan);
                                    $('#pendahuluanErrorMsg').text(response.responseJSON.errors.pendahuluan);
                                    $('#tujuanManfaatErrorMsg').text(response.responseJSON.errors.tujuan_manfaat);
                                    $('#lokasiErrorMsg').text(response.responseJSON.errors.lokasi_tempat);
                                    $('#pesertaErrorMsg').text(response.responseJSON.errors.peserta);
                                    $('#detilKegiatanErrorMsg').text(response.responseJSON.errors.detil_kegiatan);
                                    $('#penutupErrorMsg').text(response.responseJSON.errors.penutup);
                                    $('#berkasErrorMsg').text(response.responseJSON.errors.berkas);
                                    $('#tombol-simpan').html('Submit');
                                    $('#tombol-simpan').prop("disabled", false);
                                    Swal.fire({
                                        title: 'Error!',
                                        text: ' Proposal failed to submit!',
                                        type: 'error',
                                        customClass: {
                                            confirmButton: 'btn btn-primary'
                                        },
                                        buttonsStyling: false,
                                        timer: 2000
                                    });
                                }
                            });
                        }
                    })
                }
            });
        }

    }

    function getFormRkat() {
        var categorySelected = $('#id_jenis_kegiatan').val();
        var getRecentRole = $('#recentRole').val();
        if (categorySelected == 1) {
            $.ajax({
                url: "{{route('data-form-rkat')}}", 
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#showForm').removeClass('d-none');

                    var options = `<div><label for="pilihan_rkat" class="form-label">Pilihan RKAT</label>
                                    <select class="select2 form-control" id="pilihan_rkat" name="pilihan_rkat" aria-label="Default select example" style="cursor:pointer;">
                                        <option value="" id="choose_pilihan_rkat" readonly>- Pilih -</option>`;

                    if (getRecentRole === 'WAREK' || getRecentRole === 'SADM') {
                        // Tampilkan semua RKAT tanpa filter
                        if (data.length > 0) {
                            data.forEach(function(item) {
                                options += `<option value="${item.id}" data-nama-kegiatan="${item.nama_kegiatan}" data-total-rkat="${item.total}">[${item.kode_fakultas_biro}] ${item.nama_kegiatan}</option>`;
                            });
                        } else {
                            options += `<option value="" disabled>Data RKAT tidak ditemukan.</option>`;
                        }
                    } else {
                        // Filter RKAT berdasarkan idFakultasBiro
                        var idFakultasBiro = $('#catchIDFakultasBiro').val();

                        if (!idFakultasBiro || idFakultasBiro.trim() == "") {
                            options += `<option value="" disabled>Data RKAT tidak ditemukan.</option>`;
                        } else {
                            var filteredData = data.filter(function(item) {
                                return item.id_fakultas_biro == idFakultasBiro;
                            });

                            if (filteredData.length > 0) {
                                filteredData.forEach(function(item) {
                                    options += `<option value="${item.id}" data-nama-kegiatan="${item.nama_kegiatan}" data-total-rkat="${item.total}">${item.nama_kegiatan}</option>`;
                                });
                            } else {
                                options += `<option value="" disabled>Data RKAT tidak ditemukan.</option>`;
                            }
                        }
                    }
                    
                    options += `</select></div>`;
                    document.getElementById("showForm").innerHTML = options;

                    function formatRupiah(angka) {
                        return 'Rp' + angka.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' }).replace('Rp','');
                    }

                    $('#pilihan_rkat').on('change', function() {
                        var selectedOption = $(this).find('option:selected');
                        var selectedName = selectedOption.data('nama-kegiatan');
                        var selectedTotal = selectedOption.data('total-rkat');
                        $('#nama_kegiatan').val(selectedName);
                        var formattedTotal = formatRupiah(Number(selectedTotal));
                        $('#tampilkan-total').text('Total Anggaran: ' + formattedTotal);
                        $('#tampilkan-total').data('rkat_total', Number(selectedTotal));
                    });
                },
                error: function(xhr, status, error) {
                    $('#kategoriErrorMsg').text('Gagal mengambil data RKAT, silakan coba lagi.');
                    console.error('Error: ' + error);
                }

            });
        } else {
            $('#showForm').addClass('d-none');
            $('#tampilkan-total').text('');
            $('#tampilkan-total').data('rkat_total', 0);
        }
    }
    $('#choose_pilihan_rkat').attr('disabled','disabled');
    $('#pilih_sumber_anggaran').attr('disabled','disabled');

    $('select[name="id_fakultas_biro"]').on('change', function() {
        $('#id_prodi_biro').empty();
        var facultyID = $(this).val();
        if(facultyID) {
            $.ajax({
                url: '{{route("list-faculties", ":id")}}'.replace(":id", facultyID),
                type: "GET",
                dataType: "json",
                success:function(data) { 
                    $('select[name="id_prodi_biro"]').removeClass('d-none');
                    $('select[name="id_prodi_biro"]').append('<option value="">'+ '- Pilih prodi -' +'</option>');
                    $.each(data, function(key, value) {
                    $('select[name="id_prodi_biro"]').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                }
            });
        }else{
            $('select[name="id_prodi_biro"]').removeClass('d-none');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        let i = 0;

        // Fungsi untuk menambah baris
        $("#tombol-add-sarpras").click(function () {
            i++;
            const newRow = `
                <tr>
                    <td><input type="date" class="form-control" name="kolom[${i}][tgl_kegiatan]" placeholder="mm/dd/yyyy" /></td>
                    <td><input type="text" class="form-control" name="kolom[${i}][sarpras_item]" /></td>
                    <td><input type="number" class="form-control" name="kolom[${i}][jumlah]" min="0" /></td>
                    <td>
                        <select class="select2 form-select" name="kolom[${i}][sumber_sarpras]">
                            <option value="1">Kampus</option>
                            <option value="2">Mandiri</option>
                        </select>
                    </td>
                    <td><input type="text" class="form-control" name="kolom[${i}][ket]" /></td>
                    <td><button type="button" class="btn btn-danger remove-tr"><i class="bx bx-trash"></i></button></td>
                </tr>
            `;
            $("#dynamicAddRemove tbody").append(newRow);
        });


        // Fungsi untuk menghapus baris
        $(document).on("click", ".remove-tr", function () {
            $(this).closest("tr").remove();
        });
    });


    document.addEventListener('DOMContentLoaded', function(){
        let j = 0;
        $("#tombol-add-anggaran").click(function() {
            j++;
            const bariBaru = `    
                <tr>
                    <td><input type="text" class="form-control" name="rows[${j}][item]" placeholder="Input nama item" /></td>
                    <td><input type="number" class="form-control biaya_satuan" name="rows[${j}][biaya_satuan]" min="0" onkeyup="OnChange(this)" /></td>
                    <td><input type="number" class="form-control quantity" name="rows[${j}][quantity]" min="0" onkeyup="OnChange(this)" /></td>
                    <td><input type="number" class="form-control frequency" name="rows[${j}][frequency]" min="0" onkeyup="OnChange(this)" /></td>
                    <td><input type="text" class="form-control total_biaya" name="rows[${j}][total_biaya]" readonly style="cursor: no-drop;" /></td>
                    <td><select class="select2 form-select" id="sumber_anggaran" name="rows[${j}][sumber_anggaran]" onchange="calculateGrandTotal()" style="cursor:pointer;">
                        <option value="1">Kampus</option>
                        <option value="2">Mandiri</option>
                    </select></td>
                    <td><button type="button" class="btn btn-danger remove-tr-anggaran"><i class="bx bx-trash"></i></button></td>
                </tr>
            `;
            $("#dynamicAddRemoveAnggaran tbody").append(bariBaru);
            calculateGrandTotal();
    
        });
    
        $(document).on('click', '.remove-tr-anggaran', function(){  
            $(this).closest('tr').remove();
            calculateGrandTotal();
        });
    });

    var alertShown = false; // Flag untuk melacak apakah alert sudah ditampilkan
    function calculateGrandTotal() {
        var total = 0;

        // Hitung Grand Total untuk semua baris
        $('#table-body tr').each(function () {
            var rowTotal = parseInt($(this).find('.total_biaya').val()) || 0;
            total += rowTotal; // Tambahkan ke total, tanpa memeriksa sumber
        });

        // Format Grand Total sebagai Rupiah dan update elemen grand-total
        var formattedTotal = formatRupiah(total);
        $('#grand-total').text(formattedTotal);

        // Validasi hanya untuk kategori Kampus
        var totalRkat = $('#tampilkan-total').data('rkat_total') || 0; // Anggaran total RKAT
        $('#table-body tr').each(function () {
            var sumber = $(this).find('#sumber_anggaran').val(); // Ambil sumber
            if (sumber === "1") { // Hanya cek jika kategori adalah Kampus
                var rowTotal = parseInt($(this).find('.total_biaya').val()) || 0;
                if (totalRkat > 0 && rowTotal > totalRkat && !alertShown) {
                    alert('Total biaya melebihi total RKAT sebesar ' + formatRupiah(totalRkat));
                    alertShown = true; // Set flag menjadi true agar alert tidak muncul lagi
                }
            }
        });
    }



    function formatRupiah(angka) {
        return 'Rp' + angka.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' }).replace('Rp', '');
    }

    addEventListeners();

    function addEventListeners() {
        $('.biaya_satuan, .quantity, .frequency').off('keyup').on('keyup', function() {
            var rowIndex = $(this).closest('tr').index();
            OnChange(rowIndex);
        });
    }

    function OnChange(element) {
        var row = $(element).closest('tr');
        var biaya_satuan = parseInt(row.find('.biaya_satuan').val()) || 0;
        var quantity = parseInt(row.find('.quantity').val()) || 0;
        var frequency = parseInt(row.find('.frequency').val()) || 0;
        var result = biaya_satuan * quantity * frequency;
        if (!isNaN(result)) {
            row.find('.total_biaya').val(result);
            calculateGrandTotal();
        }
    }


    $('.addField').click(function(){
        $('.firstRow').parent().append(`
            <div class="row">
                <div class="col-md-3 form-group mb-3">
                    <input type="text" name="nama_berkas[]" class="w-100 form-control">
                </div>
                <div class="col-md-3 form-group mb-3">
                    <input type="file" name="berkas[]" class="w-100 form-control" accept=".pdf, .jpeg, .png">
                </div>
                <div class="col-md-3 form-group mb-3">
                    <input type="text" name="keterangan[]" class="w-100 form-control">
                </div>
                <div class="col-md-3 form-group mb-3">
                    <button type=""button" class="btn btn-danger mb-3 deleteRow"><i class="bx bx-trash bx-xs"></i></button>
                </div>
            </div>
        `);
    });

    $(document).on('click','.deleteRow', function(){
        $(this).parent().parent().remove();
    });

    // Editor Pendahuluan
    document.addEventListener('DOMContentLoaded', function() {
          if (document.getElementById('pendahuluan')) {
              var editor = new Quill('#editor-pendahuluan', {
                  theme: 'snow'
              });
              var quillEditor = document.getElementById('pendahuluan');
              editor.on('text-change', function() {
                  quillEditor.value = editor.root.innerHTML;
              });

              quillEditor.addEventListener('input', function() {
                  editor.root.innerHTML = quillEditor.value;
              });
          }

          if (document.getElementById('tujuan_manfaat')) {
              var editor1 = new Quill('#editor-tujuan-manfaat', {
                  theme: 'snow'
              });
              var quillEditor1 = document.getElementById('tujuan_manfaat');
              editor1.on('text-change', function() {
                  quillEditor1.value = editor1.root.innerHTML;
              });

              quillEditor1.addEventListener('input', function() {
                  editor1.root.innerHTML = quillEditor1.value;
              });
          }          

          if (document.getElementById('detil_kegiatan')) {
              var editor2 = new Quill('#editor-detil-kegiatan', {
                  theme: 'snow'
              });
              var quillEditor2 = document.getElementById('detil_kegiatan');
              editor2.on('text-change', function() {
                  quillEditor2.value = editor2.root.innerHTML;
              });

              quillEditor2.addEventListener('input', function() {
                  editor2.root.innerHTML = quillEditor2.value;
              });
          }

          if (document.getElementById('penutup')) {
              var editor3 = new Quill('#editor-penutup', {
                  theme: 'snow'
              });
              var quillEditor3 = document.getElementById('penutup');
              editor3.on('text-change', function() {
                  quillEditor3.value = editor3.root.innerHTML;
              });

              quillEditor3.addEventListener('input', function() {
                  editor3.root.innerHTML = quillEditor3.value;
              });
          }

          if (document.getElementById('peserta')) {
              var editor4 = new Quill('#editor-peserta', {
                  theme: 'snow'
              });
              var quillEditor4 = document.getElementById('peserta');
              editor4.on('text-change', function() {
                  quillEditor4.value = editor4.root.innerHTML;
              });

              quillEditor4.addEventListener('input', function() {
                  editor4.root.innerHTML = quillEditor4.value;
              });
          }
    });

    $('.addField').addClass('d-none');
    document.addEventListener('DOMContentLoaded', function() { 
        const switchElement = document.getElementById('switch'); 
        const formContainer = document.getElementById('form-container'); 
        switchElement.addEventListener('change', function() { 
            if (switchElement.checked) { 
                $('.addField').removeClass('d-none');
                formContainer.innerHTML = `<form id="form1"> 
                        <label for="berkas" class="form-label">Berkas </label>
                        <input type="file" id="berkas" name="berkas[]" class="w-100 form-control" accept=".pdf, .docx">
                        <div style="font-size: 11px;" class="mt-2"><i class="text-muted">Format lampiran: *.pdf atau *.docx | max. 2MB</i></div>
                        <span class="text-danger" id="berkasErrorMsg" style="font-size: 10px;"></span>
                </form> `; 
            } else { $('.addField').addClass('d-none');
            formContainer.innerHTML = `<form id="form2">
                        <label for="link_gdrive" class="form-label">Link G-Drive </label>
                        <input type="text" id="link_gdrive" name="link_gdrive[]"
                            class="form-control"
                            placeholder="https://drive.google.com/file/"
                            autocomplete="off" />
                </form> `; 

            } 
        }); // Initialize form on page load 
        switchElement.dispatchEvent(new Event('change')); 
    });

    
</script>

@endsection