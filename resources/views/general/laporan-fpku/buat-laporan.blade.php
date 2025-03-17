@extends('layouts.backend')
@section('title','Laporan FPKU')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="javascript:void(0)">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('index-laporan-fpku')}}">@yield('title')</a>
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

<div class="container-fluid flex-grow-1">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                        <!-- MULAI TOMBOL TAMBAH -->
                        <div class="mb-3">
                            <a href="{{route('index-laporan-fpku')}}"><button type="button" class="btn btn-outline-secondary"><i class="bx bx-chevron-left"></i>Back</button></a> 
                        </div>                        
                        <!-- AKHIR TOMBOL -->
                        <div class="bs-stepper wizard-vertical horizontal">
                            <div class="bs-stepper-header mt-3">
                                <div class="step" data-target="#page-1">
                                    <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">1</span>
                                    <span class="bs-stepper-label mt-1">
                                        <span class="bs-stepper-title">Informasi Kegiatan</span>
                                        <span class="bs-stepper-subtitle">Isi Informasi Kegiatan</span>
                                    </span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                <div class="step" data-target="#page-2">
                                    <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">2</span>
                                    <span class="bs-stepper-label mt-1">
                                        <span class="bs-stepper-title">Hasil dan Evaluasi Kegiatan</span>
                                        <span class="bs-stepper-subtitle">Hasil dan Evaluasi Kegiatan</span>
                                    </span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                <div class="step" data-target="#page-3">
                                    <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">3</span>
                                    <span class="bs-stepper-label mt-1">
                                        <span class="bs-stepper-title">Anggaran</span>
                                        <span class="bs-stepper-subtitle">Lengkapi Data Anggaran</span>
                                    </span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                <div class="step" data-target="#page-4">
                                    <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">4</span>
                                    <span class="bs-stepper-label mt-1">
                                        <span class="bs-stepper-title">Lampiran</span>
                                        <span class="bs-stepper-subtitle">Upload Lampiran</span>
                                    </span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                <div class="step" data-target="#page-5">
                                    <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">5</span>
                                    <span class="bs-stepper-label mt-1">
                                        <span class="bs-stepper-title">Penutup</span>
                                        <span class="bs-stepper-subtitle">Isi Penutup</span>
                                    </span>
                                    </button>
                                </div>
                            </div>

                            <div class="bs-stepper-content">
                              <form onSubmit="return false" id="form-laporan-proposal">
                                <input type="hidden" name="id_fpku" id="id_fpku" value="{{$id['id']}}">

                                <!-- Informasi Kegiatan -->
                                <div id="page-1" class="content mt-3">
                                    <div class="content-header mb-3">
                                        <h6 class="mb-0">Informasi Kegiatan</h6>
                                        <small>Lengkapi Informasi Kegiatan</small>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                                            <input type="text" id="nama_kegiatan" name="nama_kegiatan" value="{{$getDataFpku->nama_kegiatan}}" class="form-control">
                                            <span class="text-danger" id="namaKegiatanErrorMsg" style="font-size: 10px;"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="tgl_kegiatan" class="form-label">Tanggal Kegiatan</label>
                                            <input type="date" class="form-control" id="tgl_kegiatan" name="tgl_kegiatan" value="{{$getDataFpku->tgl_kegiatan}}" placeholder="mm/dd/yyyy" />
                                            <span class="text-danger" id="tglKegiatanErrorMsg" style="font-size: 10px;"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="id_fakultas_biro">Fakultas atau Unit</label>
                                            <select class="select2 form-control border border-primary" id="id_fakultas_biro" name="id_fakultas_biro" aria-label="Default select example" style="cursor:pointer;">
                                              <option value="" id="choose_faculty" readonly>- Select faculty or unit -</option>
                                              @foreach($getFakultasBiro as $fakultasbiro)
                                                  <option value="{{$fakultasbiro->id}}">{{str_replace('Fakultas','',$fakultasbiro->nama_fakultas_biro)}}</option>
                                              @endforeach
                                            </select>
                                            <span class="text-danger" id="fakultasErrorMsg" style="font-size: 10px;"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="id_prodi_biro">Prodi atau Biro</label>
                                            <select class="select2 form-control border border-primary" id="id_prodi_biro" name="id_prodi_biro" aria-label="Default select example" style="cursor:pointer;">
                                              <option value="" class="d-none">- Pilih prodi -</option>
                                            </select>
                                            <span class="text-danger" id="prodiErrorMsg" style="font-size: 10px;"></span>
                                        </div>
                                        
                                        <div class="col-md-12">
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
                                            <button class="btn btn-label-secondary btn-prev mt-3" disabled> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                                <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                            </button>
                                            <button class="btn btn-primary btn-next mt-3"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="bx bx-chevron-right bx-sm me-sm-n2"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Evaluasi Kegiatan -->
                                <div id="page-2" class="content mt-3">
                                  <div class="content-header mb-3">
                                    <h6 class="mb-0">Hasil dan Evaluasi Kegiatan</h6>
                                    <small>Input Hasil Kegiatan</small>
                                  </div>
                                  <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="hasil_kegiatan" class="form-label">Hasil dan Evaluasi Kegiatan</label>
                                        <div id="editor-hasil-kegiatan" class="mb-3" style="height: 300px;"></div>
                                        <textarea rows="3" class="mb-3 d-none" name="hasil_kegiatan" id="hasil_kegiatan"></textarea>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="evaluasi_catatan_kegiatan" class="form-label">Evaluasi dan Catatan Kegiatan</label>
                                        <div id="editor-evaluasi" class="mb-3" style="height: 300px;"></div>
                                        <textarea rows="3" class="mb-3 d-none" name="evaluasi_catatan_kegiatan" id="evaluasi_catatan_kegiatan"></textarea>
                                    </div>
                                    
                                    <div class="col-12 d-flex justify-content-between">
                                      <button class="btn btn-primary btn-prev"> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                      </button>
                                      <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="bx bx-chevron-right bx-sm me-sm-n2"></i></button>
                                    </div>
                                  </div>
                                </div>

                                <!-- Rencana Anggaran -->
                                <div id="page-3" class="content mt-3">
                                  <div class="content-header mb-3">
                                    <h6 class="mb-0">Rencana Anggaran</h6>
                                    <small>Data Rencana Anggaran</small>
                                  </div>
                                  <div class="row g-3">
                                    <table class="table table-borderless" id="dynamicAddRemoveAnggaran">
                                        <tr>
                                            <th>Item</th>
                                            <th>Biaya Satuan</th>
                                            <th width="12%;">Qty</th>
                                            <th width="12%;">Freq.</th>
                                            <th>Total Biaya</th>
                                            <th>Sumber</th>
                                            <th>Aksi</th>
                                        </tr>
                                        <tr>
                                            <td><input type="text" class="form-control" id="item" name="rows[0][item]" value="" placeholder="Input item" /></td>
                                            <td><input type="number" class="form-control" id="biaya_satuan" name="rows[0][biaya_satuan]" value="" min="0" onkeyup="OnChange(this.value)" /></td>
                                            <td><input type="number" class="form-control" id="quantity" name="rows[0][quantity]" value="" min="0" onkeyup="OnChange(this.value)" /></td>
                                            <td><input type="number" class="form-control" id="frequency" name="rows[0][frequency]" value="" min="0" onkeyup="OnChange(this.value)" /></td>
                                            <td><input type="text" class="form-control" id="total_biaya" name="rows[0][total_biaya]" value="" min="0" readonly style="cursor: no-drop;" /></td>
                                            <td><select class="select2 form-select" id="sumber" name="rows[0][sumber]" style="cursor:pointer;">
                                              <option value="1">Kampus</option>
                                              <option value="2">Mandiri</option>
                                            </select></td>
                                            <td><button type="button" class="btn btn-warning btn-block" id="tombol-add-anggaran"><i class="bx bx-plus-circle"></i></button></td>
                                        </tr>
                                      </table>
                                      <div class="divider">
                                        <div class="divider-text">Masukkan Data Realisasi Anggaran</div>
                                    </div>
                                    <div class="content-header mb-3">
                                        <h6 class="mb-0">Realisasi Anggaran</h6>
                                        <small>Data Realisasi Anggaran</small>
                                      </div>
                                    <table class="table table-borderless" id="dynamicRealisasiAnggaran">
                                        <tr>
                                            <th>Item</th>
                                            <th>Biaya Satuan</th>
                                            <th width="12%;">Qty</th>
                                            <th width="12%;">Freq.</th>
                                            <th>Total Biaya</th>
                                            <th>Sumber</th>
                                            <th>Aksi</th>
                                        </tr>
                                        <tr>
                                            <td><input type="text" class="form-control" id="r_item" name="baris[0][r_item]" value="" placeholder="Input item" /></td>
                                            <td><input type="number" class="form-control" id="r_biaya_satuan" name="baris[0][r_biaya_satuan]" value="" min="0" onkeyup="OnChange2(this.value)" /></td>
                                            <td><input type="number" class="form-control" id="r_quantity" name="baris[0][r_quantity]" value="" min="0" onkeyup="OnChange2(this.value)" /></td>
                                            <td><input type="number" class="form-control" id="r_frequency" name="baris[0][r_frequency]" value="" min="0" onkeyup="OnChange2(this.value)" /></td>
                                            <td><input type="text" class="form-control" id="r_total_biaya" name="baris[0][r_total_biaya]" value="" min="0" readonly style="cursor: no-drop;" /></td>
                                            <td><select class="select2 form-select" id="r_sumber" name="baris[0][r_sumber]" style="cursor:pointer;">
                                              <option value="1">Kampus</option>
                                              <option value="2">Mandiri</option>
                                              <option value="3">Hibah</option>
                                            </select></td>
                                            <td><button type="button" class="btn btn-warning btn-block" id="tombol-realisasi-anggaran"><i class="bx bx-plus-circle"></i></button></td>
                                        </tr>
                                    </table>
                                    <div class="col-12 d-flex justify-content-between">
                                      <button class="btn btn-primary btn-prev"> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                      </button>
                                      <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="bx bx-chevron-right bx-sm me-sm-n2"></i></button>
                                    </div>
                                  </div>
                                </div>

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
                                        <p style="font-size: 12px;" class="text-warning"><i>*Silakan klik Next jika tidak ada data atau berkas yang ingin dilampirkan.</i></p>
                                        <div class="col-12 d-flex justify-content-between">
                                            <button class="btn btn-primary btn-prev"> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                              <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                            </button>
                                            <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="bx bx-chevron-right bx-sm me-sm-n2"></i></button>
                                          </div>
                                      </div>
                                    </div>

                                <!-- Penutup -->
                                <div id="page-5" class="content mt-3">
                                  <div class="content-header mb-3">
                                      <h6 class="mb-0">Penutup</h6>
                                      <small>Lengkapi Penutup Laporan Proposal</small>
                                  </div>
                                  <div class="row g-3">
                                      <div class="col-md-12">
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
                        let text = value;
                    $('select[name="id_prodi_biro"]').append('<option value="'+ key +'">'+ text.replace('Program Studi','') +'</option>');
                    });
                }
            });
        }else{
            $('select[name="id_prodi_biro"]').removeClass('d-none');
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
            if ($("#form-laporan-proposal").length > 0) {
            $("#form-laporan-proposal").validate({
                    submitHandler: function (form) {
                        var actionType = $('#tombol-simpan').val();
                        var formData = new FormData($("#form-laporan-proposal")[0]);
                        $('#tombol-simpan').html('Saving..');

                        $.ajax({
                            data: formData,
                            contentType: false,
                            processData: false,
                            url: "{{ route('insert-laporan-fpku') }}",
                            type: "POST",
                            dataType: 'json',
                            success: function (data) {
                                $('#form-laporan-proposal').trigger("reset");
                                $('#tombol-simpan').html('Save');
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
                                window.location = '{{ route("index-laporan-fpku") }}';
                            },
                            error: function (response) {
                                $('#tglKegiatanErrorMsg').text(response.responseJSON.errors.tgl_kegiatan);
                                $('#fakultasErrorMsg').text(response.responseJSON.errors.id_fakultas_biro);
                                $('#prodiErrorMsg').text(response.responseJSON.errors.id_prodi_biro);
                                $('#namaKegiatanErrorMsg').text(response.responseJSON.errors.nama_kegiatan);
                                $('#pendahuluanErrorMsg').text(response.responseJSON.errors.pendahuluan);
                                $('#tujuanManfaatErrorMsg').text(response.responseJSON.errors.tujuan_manfaat);
                                $('#lokasiErrorMsg').text(response.responseJSON.errors.lokasi_tempat);
                                $('#pesertaErrorMsg').text(response.responseJSON.errors.peserta);
                                $('#detilKegiatanErrorMsg').text(response.responseJSON.errors.detil_kegiatan);
                                $('#penutupErrorMsg').text(response.responseJSON.errors.penutup);
                                $('#berkasErrorMsg').text(response.responseJSON.errors.berkas);
                                $('#tombol-simpan').html('Save');
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

    var j = 0;
    $("#tombol-add-anggaran").click(function(){
      ++j;

      $("#dynamicAddRemoveAnggaran").append('<tr><td><input type="text" class="form-control" id="item" name="rows['+j+'][item]" value="" placeholder="Input item" /></td><td><input type="number" class="form-control" id="biaya_satuan" name="rows['+j+'][biaya_satuan]" value="" min="0" onkeyup="OnChange(this.value)" /></td><td><input type="number" class="form-control" id="quantity" name="rows['+j+'][quantity]" value="" min="0" onkeyup="OnChange(this.value)" /></td><td><input type="number" class="form-control" id="frequency" name="rows['+j+'][frequency]" value="" min="0" onkeyup="OnChange(this.value)" /></td><td><input type="text" class="form-control" id="total_biaya" name="rows['+j+'][total_biaya]" value="" min="0" readonly style="cursor: no-drop;" /></td><td><select class="select2 form-select" id="sumber" name="rows['+j+'][sumber]" style="cursor:pointer;"><option value="1">Kampus</option><option value="2">Mandiri</option></select></td><td><button type="button" class="btn btn-danger remove-tr-anggaran"><i class="bx bx-trash"></i></button></td></tr>');

      OnChange();

    });

    $(document).on('click', '.remove-tr-anggaran', function(){  
        $(this).parents('tr').remove();
    });

    function OnChange(value) {
        var biaya_satuan = document.querySelector('input[name="rows['+j+'][biaya_satuan]"]').value;
        var quantity = document.querySelector('input[name="rows['+j+'][quantity]"]').value;
        var frequency = document.querySelector('input[name="rows['+j+'][frequency]"]').value;
        var result = parseInt(biaya_satuan) * parseInt(quantity) * parseInt(frequency);
        if (!isNaN(result)) {
            document.querySelector('input[name="rows['+j+'][total_biaya]"]').value = result;
        }
    }

    var n = 0;
    $("#tombol-realisasi-anggaran").click(function(){
      ++n;

      $("#dynamicRealisasiAnggaran").append('<tr><td><input type="text" class="form-control" id="r_item" name="baris['+n+'][r_item]" value="" placeholder="Input item" /></td><td><input type="number" class="form-control" id="r_biaya_satuan" name="baris['+n+'][r_biaya_satuan]" value="" min="0" onkeyup="OnChange2(this.value)" /></td><td><input type="number" class="form-control" id="r_quantity" name="baris['+n+'][r_quantity]" value="" min="0" onkeyup="OnChange2(this.value)" /></td><td><input type="number" class="form-control" id="r_frequency" name="baris['+n+'][r_frequency]" value="" min="0" onkeyup="OnChange2(this.value)" /></td><td><input type="text" class="form-control" id="r_total_biaya" name="baris['+n+'][r_total_biaya]" value="" min="0" readonly style="cursor: no-drop;" /></td><td><select class="select2 form-select" id="r_sumber" name="baris['+n+'][r_sumber]" style="cursor:pointer;"><option value="1">Kampus</option><option value="2">Mandiri</option></select></td><td><button type="button" class="btn btn-danger remove-tr-realisasi-anggaran"><i class="bx bx-trash"></i></button></td></tr>');

      OnChange2();

    });

    $(document).on('click', '.remove-tr-realisasi-anggaran', function(){  
        $(this).parents('tr').remove();
    });

    function OnChange2(value) {
        var r_biaya_satuan = document.querySelector('input[name="baris['+n+'][r_biaya_satuan]"]').value;
        var r_quantity = document.querySelector('input[name="baris['+n+'][r_quantity]"]').value;
        var r_frequency = document.querySelector('input[name="baris['+n+'][r_frequency]"]').value;
        var r_result = parseInt(r_biaya_satuan) * parseInt(r_quantity) * parseInt(r_frequency);
        if (!isNaN(r_result)) {
            document.querySelector('input[name="baris['+n+'][r_total_biaya]"]').value = r_result;
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

    document.addEventListener('DOMContentLoaded', function() {
          if (document.getElementById('hasil_kegiatan')) {
              var editor = new Quill('#editor-hasil-kegiatan', {
                  theme: 'snow'
              });
              var quillEditor = document.getElementById('hasil_kegiatan');
              editor.on('text-change', function() {
                  quillEditor.value = editor.root.innerHTML;
              });

              quillEditor.addEventListener('input', function() {
                  editor.root.innerHTML = quillEditor.value;
              });
          }

          if (document.getElementById('evaluasi_catatan_kegiatan')) {
              var editor1 = new Quill('#editor-evaluasi', {
                  theme: 'snow'
              });
              var quillEditor1 = document.getElementById('evaluasi_catatan_kegiatan');
              editor1.on('text-change', function() {
                  quillEditor1.value = editor1.root.innerHTML;
              });

              quillEditor1.addEventListener('input', function() {
                  editor1.root.innerHTML = quillEditor1.value;
              });
          }   
          
          if (document.getElementById('pendahuluan')) {
              var editor2 = new Quill('#editor-pendahuluan', {
                  theme: 'snow'
              });
              var quillEditor2 = document.getElementById('pendahuluan');
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

          if (document.getElementById('tujuan_manfaat')) {
              var editor4 = new Quill('#editor-tujuan-manfaat', {
                  theme: 'snow'
              });
              var quillEditor4 = document.getElementById('tujuan_manfaat');
              editor4.on('text-change', function() {
                  quillEditor4.value = editor4.root.innerHTML;
              });

              quillEditor4.addEventListener('input', function() {
                  editor4.root.innerHTML = quillEditor4.value;
              });
          }          

          if (document.getElementById('detil_kegiatan')) {
              var editor5 = new Quill('#editor-detil-kegiatan', {
                  theme: 'snow'
              });
              var quillEditor5 = document.getElementById('detil_kegiatan');
              editor5.on('text-change', function() {
                  quillEditor5.value = editor5.root.innerHTML;
              });

              quillEditor5.addEventListener('input', function() {
                  editor5.root.innerHTML = quillEditor5.value;
              });
          }

          if (document.getElementById('peserta')) {
              var editor6 = new Quill('#editor-peserta', {
                  theme: 'snow'
              });
              var quillEditor6 = document.getElementById('peserta');
              editor6.on('text-change', function() {
                  quillEditor6.value = editor6.root.innerHTML;
              });

              quillEditor6.addEventListener('input', function() {
                  editor6.root.innerHTML = quillEditor6.value;
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