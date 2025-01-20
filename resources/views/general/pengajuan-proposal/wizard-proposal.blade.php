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

<div class="container-fluid flex-grow-1">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                        <!-- MULAI TOMBOL TAMBAH -->
                        <div class="mb-3">
                            <a href="{{route('submission-of-proposal.index')}}"><button type="button" class="btn btn-outline-secondary"><i class="bx bx-chevron-left"></i>Back</button></a> 
                        </div>                        
                        <!-- AKHIR TOMBOL -->
                        <div class="bs-stepper wizard-vertical vertical">
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
                              <form onSubmit="return false" id="form-proposal">

                                <!-- Informasi Utama -->
                                <div id="page-1" class="content mt-3">
                                    <div class="content-header mb-3">
                                        <h6 class="mb-0">Informasi Utama</h6>
                                        <small>Lengkapi Informasi Utama.</small>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="id_jenis_kegiatan">Kategori Proposal</label>
                                            <select class="select2 form-control" id="id_jenis_kegiatan" name="id_jenis_kegiatan" aria-label="Default select example" style="cursor:pointer;">
                                              <option value="" id="choose_jenis_kegiatan" readonly>- Pilih -</option>
                                              @forEach($getJenisKegiatan as $data)
                                              <option value="{{$data->id}}">{{$data->nama_jenis_kegiatan}}</option>
                                              @endforeach
                                            </select>
                                            <span class="text-danger" id="kategoriErrorMsg" style="font-size: 10px;"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="tgl_event" class="form-label">Tanggal Kegiatan</label>
                                            <input type="date" class="form-control" id="tgl_event" name="tgl_event" value="" placeholder="mm/dd/yyyy" />
                                            <span class="text-danger" id="tglKegiatanErrorMsg" style="font-size: 10px;"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="id_fakultas">Fakultas atau Unit</label>
                                            <select class="select2 form-control border border-primary" id="id_fakultas" name="id_fakultas" aria-label="Default select example" style="cursor:pointer;">
                                              <option value="" id="choose_faculty" readonly>- Select faculty or unit -</option>
                                              @foreach($getFakultas as $faculty)
                                                  <option value="{{$faculty->id}}">{{$faculty->nama_fakultas}}</option>
                                              @endforeach
                                            </select>
                                            <span class="text-danger" id="fakultasErrorMsg" style="font-size: 10px;"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="id_prodi">Prodi atau Biro</label>
                                            <select class="select2 form-control border border-primary" id="id_prodi" name="id_prodi" aria-label="Default select example" style="cursor:pointer;">
                                              <option value="" class="d-none">- Pilih prodi -</option>
                                            </select>
                                            <span class="text-danger" id="prodiErrorMsg" style="font-size: 10px;"></span>
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

                                <!-- Sarana Prasarana -->
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
                                    <small>Cantumkan Rencana Anggaran.</small>
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
                                            <option value="3">Hibah</option>
                                          </select></td>
                                          <td><button type="button" class="btn btn-warning btn-block" id="tombol-add-anggaran"><i class="bx bx-plus-circle"></i></button></td>
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

                                <!-- Lampiran Proposal -->
                              <div id="page-4" class="content mt-3">
                                <div class="content-header mb-3">
                                  <h6 class="mb-0">Lampiran Proposal <i>(Opsional)</i></h6>
                                  <small>Upload Lampiran Proposal.</small>
                                </div>
                                <div class="row g-3">                                    
                                    <div>
                                        <div class="row firstRow">
                                            <div class="col-md-3 form-group mb-3">
                                                <label for="nama_berkas" class="form-label">Nama Berkas</label>
                                                <input type="text" name="nama_berkas[]" class="w-100 form-control">
                                            </div>
                                            <div class="col-md-3 form-group mb-3">
                                                <label for="berkas" class="form-label">Berkas </label>
                                                <input type="file" id="berkas" name="berkas[]" class="w-100 form-control" accept=".pdf, .docx">
                                                <div style="font-size: 11px;" class="mt-2"><i class="text-muted">*format lampiran: *.pdf atau *.docx</i></div>
                                                <span class="text-danger" id="berkasErrorMsg" style="font-size: 10px;"></span>
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
                        $('#tombol-simpan').html('Sumbitting..');

                        $.ajax({
                            data: formData,
                            contentType: false,
                            processData: false,
                            url: "{{ route('insert-proposal') }}",
                            type: "POST",
                            dataType: 'json',
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
                                $('#fakultasErrorMsg').text(response.responseJSON.errors.id_fakultas);
                                $('#prodiErrorMsg').text(response.responseJSON.errors.id_prodi);
                                $('#namaKegiatanErrorMsg').text(response.responseJSON.errors.nama_kegiatan);
                                $('#pendahuluanErrorMsg').text(response.responseJSON.errors.pendahuluan);
                                $('#tujuanManfaatErrorMsg').text(response.responseJSON.errors.tujuan_manfaat);
                                $('#lokasiErrorMsg').text(response.responseJSON.errors.lokasi_tempat);
                                $('#pesertaErrorMsg').text(response.responseJSON.errors.peserta);
                                $('#detilKegiatanErrorMsg').text(response.responseJSON.errors.detil_kegiatan);
                                $('#penutupErrorMsg').text(response.responseJSON.errors.penutup);
                                $('#berkasErrorMsg').text(response.responseJSON.errors.berkas);
                                $('#tombol-simpan').html('Submit');
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

    $('select[name="id_fakultas"]').on('change', function() {
        $('#id_prodi').empty();
        var facultyID = $(this).val();
        if(facultyID) {
            $.ajax({
                url: '{{route("list-faculties", ":id")}}'.replace(":id", facultyID),
                type: "GET",
                dataType: "json",
                success:function(data) { 
                    $('select[name="id_prodi"]').removeClass('d-none');
                    $('select[name="id_prodi"]').append('<option value="">'+ '- Pilih prodi -' +'</option>');
                    $.each(data, function(key, value) {
                    $('select[name="id_prodi"]').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                }
            });
        }else{
            $('select[name="id_prodi"]').removeClass('d-none');
        }
    });

    var i = 0;
    $("#tombol-add-sarpras").click(function(){
        ++i;

        $("#dynamicAddRemove").append('<tr><td><input type="date" class="form-control" id="tgl_kegiatan" name="kolom['+i+'][tgl_kegiatan]" value="" placeholder="mm/dd/yyyy" /></td><td><textarea class="form-control" id="sarpras_item" name="kolom['+i+'][sarpras_item]" rows="3"></textarea></td><td><input type="number" class="form-control" id="jumlah" name="kolom['+i+'][jumlah]" value="" min="0" /></td><td><select class="select2 form-select" id="sumber" name="kolom['+i+'][sumber]" style="cursor:pointer;"><option value="1">Kampus</option><option value="2">Mandiri</option><option value="3">Hibah</option></select></td><td><textarea class="form-control" id="ket" name="kolom['+i+'][ket]" rows="3"></textarea></td><td><button type="button" class="btn btn-danger remove-tr"><i class="bx bx-trash"></i></button></td></tr>');
    });

    $(document).on('click', '.remove-tr', function(){  
        $(this).parents('tr').remove();
    });

    var j = 0;
    $("#tombol-add-anggaran").click(function(){
      ++j;

      $("#dynamicAddRemoveAnggaran").append('<tr><td><input type="text" class="form-control" id="item" name="rows['+j+'][item]" value="" placeholder="Input item" /></td><td><input type="number" class="form-control" id="biaya_satuan" name="rows['+j+'][biaya_satuan]" value="" min="0" onkeyup="OnChange(this.value)" /></td><td><input type="number" class="form-control" id="quantity" name="rows['+j+'][quantity]" value="" min="0" onkeyup="OnChange(this.value)" /></td><td><input type="number" class="form-control" id="frequency" name="rows['+j+'][frequency]" value="" min="0" onkeyup="OnChange(this.value)" /></td><td><input type="text" class="form-control" id="total_biaya" name="rows['+j+'][total_biaya]" value="" min="0" readonly style="cursor: no-drop;" /></td><td><select class="select2 form-select" id="sumber" name="rows['+j+'][sumber]" style="cursor:pointer;"><option value="1">Kampus</option><option value="2">Mandiri</option><option value="3">Hibah</option></select></td><td><button type="button" class="btn btn-danger remove-tr-anggaran"><i class="bx bx-trash"></i></button></td></tr>');

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

</script>

@endsection