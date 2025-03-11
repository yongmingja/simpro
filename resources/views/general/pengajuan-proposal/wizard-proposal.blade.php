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
@php
    if(session()->get('selected_peran') == ''){
        $getPeran = App\Models\Master\JabatanPegawai::leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
            ->where('jabatan_pegawais.id_pegawai',Auth::user()->id)
            ->select('jabatans.kode_jabatan','jabatan_pegawais.id_fakultas_biro')
            ->first();
        $recentRole = $getPeran->kode_jabatan;
    } else {
        $recentRole = session()->get('selected_peran');
    }                        
@endphp
<div class="container-fluid flex-grow-1">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                        <!-- MULAI TOMBOL TAMBAH -->
                        <div class="mb-3">
                            <a href="{{route('submission-of-proposal.index')}}"><button type="button" class="btn btn-outline-secondary"><i class="bx bx-chevron-left"></i>Back</button></a> 
                        </div>       
                        
                        <input type="hidden" name="getRole" id="getRole" value="{{$recentRole}}">            
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
                                        <span class="bs-stepper-title">Lampiran</span>
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

                            <div class="bs-stepper-content">
                              <form onSubmit="return false" id="form-proposal">

                                <!-- Informasi Utama -->
                                <div class="container">
                                    <div id="page-1" class="content mt-3">
                                        <div class="content-header mb-3">
                                            <h6 class="mb-0">Informasi Utama</h6>
                                            <small>Lengkapi Informasi Utama.</small>
                                        </div>
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
                                            <div class="col-sm-6">
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
                                    <div id="page-2" class="content mt-3">
                                        <div class="content-header mb-3">
                                          <h6 class="mb-0">Sarana Prasarana</h6>
                                          <small>Input Sarana Prasarana.</small>
                                        </div>
                                        <div class="row g-3">
                                          <div class="col-md-12">
                                            <table class="table table-borderless" id="dynamicAddRemove">
                                              <tr>
                                                  <th>Tgl. Kegiatan</th>
                                                  <th>Sarpras</th>
                                                  <th width="12%;">Jumlah</th>
                                                  <th>Sumber Dana</th>
                                                  <th>Ket.</th>
                                                  <th>Aksi</th>
                                              </tr>
                                              <tr>
                                                  <td><input type="date" class="form-control" id="tgl_kegiatan" name="kolom[0][tgl_kegiatan]" value="" placeholder="mm/dd/yyyy" /></td>
                                                  <td><textarea class="form-control" id="sarpras_item" name="kolom[0][sarpras_item]" rows="3"></textarea></td>
                                                  <td><input type="number" class="form-control" id="jumlah" name="kolom[0][jumlah]" value="" min="0" /></td>
                                                  <td><select class="select2 form-select" id="sumber" name="kolom[0][sumber]" style="cursor:pointer;">
                                                      <option value="1">Kampus</option>
                                                      <option value="2">Mandiri</option>
                                                      <option value="3">Hibah</option>
                                                  </select></td>
                                                  <td><textarea class="form-control" id="ket" name="kolom[0][ket]" rows="3"></textarea></td>
                                                  <td><button type="button" class="btn btn-warning btn-block" id="tombol-add-sarpras"><i class="bx bx-plus-circle"></i></button></td>
                                              </tr>
                                            </table>
                                          </div>
                                          <p style="font-size: 14px;" class="text-warning"><i>*Silakan klik next jika tidak ada sarana prasarana yang dibutuhkan.</i></p>
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
                                    <div id="page-3" class="content mt-3">
                                        <div class="content-header mb-3">
                                          <h6 class="mb-0">Rencana Anggaran</h6>
                                          <small>Cantumkan Rencana Anggaran.</small>
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
                                                    <td><input type="text" class="form-control" id="item" name="rows[0][item]" value="" placeholder="Input item" /></td>
                                                    <td><input type="number" class="form-control biaya_satuan" id="biaya_satuan" name="rows[0][biaya_satuan]" value="" min="0" onkeyup="OnChange(this)" /></td>
                                                    <td><input type="number" class="form-control quantity" id="quantity" name="rows[0][quantity]" value="" min="0" onkeyup="OnChange(this)" /></td>
                                                    <td><input type="number" class="form-control frequency" id="frequency" name="rows[0][frequency]" value="" min="0" onkeyup="OnChange(this)" /></td>
                                                    <td><input type="text" class="form-control total_biaya" id="total_biaya" name="rows[0][total_biaya]" value="" min="0" readonly style="cursor: no-drop;" /></td>
                                                    <td><select class="select2 form-select" id="sumber" name="rows[0][sumber]" style="cursor:pointer;">
                                                        <option value="1">Kampus</option>
                                                        <option value="2">Mandiri</option>
                                                        <option value="3">Hibah</option>
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
                                        
                                          <p style="font-size: 14px;" class="text-warning"><i>*Silakan klik next jika tidak memiliki rencana anggaran pada proposal kegiatan.</i></p>
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
                                    <div id="page-4" class="content mt-3">
                                        <div class="content-header mb-3">
                                        <h6 class="mb-0">Lampiran Proposal <i>(Opsional)</i></h6>
                                        <small>Upload Lampiran Proposal.</small>
                                        </div>
                                        <div class="col form-group p-0">
                                                <p style="font-size: 12px; line-height:12px;" class="text-primary mt-1">Note:<br>- Anda bisa menggunakan link google drive atau unggah berkas.<br>- Jika ingin unggah berupa berkas, silakan checklist upload berkas.<br></p>
                                        </div>
                                        <input type="checkbox" id="switch" name="switch" class="mb-3">
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
                                            <p style="font-size: 14px;" class="text-warning"><i>*Silakan klik next jika tidak ada data atau berkas yang ingin dilampirkan.</i></p>
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
                                    <div id="page-5" class="content mt-3">
                                        <div class="content-header mb-3">
                                            <h6 class="mb-0">Penutup</h6>
                                            <small>Lengkapi Penutup Proposal</small>
                                        </div>
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

    document.addEventListener('DOMContentLoaded', function() {
        const userRole = $('#getRole').val(); // Ganti ini dengan logika untuk mendapatkan peran pengguna saat ini
        const selectElement = document.getElementById('id_jenis_kegiatan');
        const options = selectElement.options;

        for (let i = 0; i < options.length; i++) {
            const option = options[i];
            const optionId = option.getAttribute('data-name');
            
            if (userRole === 'WRSDP' || userRole === 'WRAK') {
                if (optionId !== 'Rektorat') {
                    option.disabled = true;
                }
            }
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
                            beforeSend: function(){
                                $("#tombol-simpan").append(
                                    '<i class="bx bx-loader-circle bx-spin text-warning"></i>'+
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
                                })
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
                    data.forEach(function(item) {
                        options += `<option value="${item.id}" data-nama-kegiatan="${item.nama_kegiatan}" data-total-rkat="${item.total}">${item.nama_kegiatan}</option>`;
                       
                    });
                    options += `</select></div>`;
                    document.getElementById("showForm").innerHTML = options;

                    function formatRupiah(angka) {
                        return 'Rp' + angka.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' }).replace('Rp','');
                    }

                    // Tambahkan event listener untuk perubahan pada pilihan_rkat
                    $('#pilihan_rkat').on('change', function() {
                        var selectedOption = $(this).find('option:selected');
                        var selectedName = selectedOption.data('nama-kegiatan');
                        var selectedTotal = selectedOption.data('total-rkat');
                        $('#nama_kegiatan').val(selectedName); // Update nilai input dengan nama kegiatan yang dipilih  
                        var formattedTotal = formatRupiah(Number(selectedTotal));
                        $('#tampilkan-total').text('Total Anggaran: '+formattedTotal);   
                        $('#tampilkan-total').data('rkat_total', Number(selectedTotal));            
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error: ' + error);
                }
            });
        } else {
            $('#showForm').addClass('d-none');
        }
    }
    $('#choose_pilihan_rkat').attr('disabled','disabled');

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

    var i = 0;
    $("#tombol-add-sarpras").click(function(){
        ++i;

        $("#dynamicAddRemove").append(`<tr>
            <td><input type="date" class="form-control" id="tgl_kegiatan" name="kolom['+i+'][tgl_kegiatan]" value="" placeholder="mm/dd/yyyy" /></td>
            <td><textarea class="form-control" id="sarpras_item" name="kolom['+i+'][sarpras_item]" rows="3"></textarea></td>
            <td><input type="number" class="form-control" id="jumlah" name="kolom['+i+'][jumlah]" value="" min="0" /></td>
            <td>
                <select class="select2 form-select" id="sumber" name="kolom['+i+'][sumber]" style="cursor:pointer;">
                    <option value="1">Kampus</option>
                    <option value="2">Mandiri</option>
                    <option value="3">Hibah</option>
                </select>
            </td>
            <td><textarea class="form-control" id="ket" name="kolom['+i+'][ket]" rows="3"></textarea></td>
            <td><button type="button" class="btn btn-danger remove-tr"><i class="bx bx-trash"></i></button></td>
        </tr>`);
    });

    $(document).on('click', '.remove-tr', function(){  
        $(this).parents('tr').remove();
    });

    var j = 0;
    $("#tombol-add-anggaran").click(function() {
        ++j;

        $("#table-body").append(`<tr>
            <td><input type="text" class="form-control" name="rows['+j+'][item]" placeholder="Input item" /></td>
            <td><input type="number" class="form-control biaya_satuan" name="rows['+j+'][biaya_satuan]" min="0" onkeyup="OnChange(this)" /></td>
            <td><input type="number" class="form-control quantity" name="rows['+j+'][quantity]" min="0" onkeyup="OnChange(this)" /></td>
            <td><input type="number" class="form-control frequency" name="rows['+j+'][frequency]" min="0" onkeyup="OnChange(this)" /></td>
            <td><input type="text" class="form-control total_biaya" name="rows['+j+'][total_biaya]" readonly style="cursor: no-drop;" /></td>
            <td><select class="select2 form-select" name="rows['+j+'][sumber]" style="cursor:pointer;">
                <option value="1">Kampus</option>
                <option value="2">Mandiri</option>
                <option value="3">Hibah</option>
            </select></td>
            <td><button type="button" class="btn btn-danger remove-tr-anggaran"><i class="bx bx-trash"></i></button></td>
        </tr>`);

        calculateGrandTotal();

    });

    $(document).on('click', '.remove-tr-anggaran', function(){  
        $(this).parents('tr').remove();
        calculateGrandTotal();
    });

    function calculateGrandTotal() {
        var total = 0;
        $('#table-body tr').each(function() {
            var rowTotal = parseInt($(this).find('.total_biaya').val()) || 0;
            total += rowTotal;
        });

        // Format total sebagai Rupiah dan update elemen grand-total
        var formattedTotal = formatRupiah(total);
        $('#grand-total').text(formattedTotal);

        // Retrieve the tampilkan total value
        var tampilkanTotal = $('#tampilkan-total').data('rkat_total') || 0;

        // Compare grand total with tampilkan total
        if (tampilkanTotal > 0 && total > tampilkanTotal) {
            $('#next-anggaran').prop("disabled", true);
            $('#tombol-page-4').prop("disabled", true);
            $('#tombol-page-5').prop("disabled", true);
            alert('Mohon maaf anda tidak bisa melanjutkan proposal karena total biaya melebihi total anggaran RKAT yang anda pilih yaitu sebesar '+ formatRupiah(tampilkanTotal) +'');
        } else {
            $('#next-anggaran').prop("disabled", false);
            $('#tombol-page-4').prop("disabled", false);
            $('#tombol-page-5').prop("disabled", false);
        }
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