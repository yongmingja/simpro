@extends('layouts.backend')
@section('title','Laporan Proposal')

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

</style>

<div class="container-fluid flex-grow-1">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                        <!-- MULAI TOMBOL TAMBAH -->
                        <div class="mb-3">
                            <a href="{{route('my-report')}}"><button type="button" class="btn btn-outline-secondary"><i class="bx bx-chevron-left"></i>Back</button></a> 
                        </div>                        
                        <!-- AKHIR TOMBOL -->
                        <div class="bs-stepper wizard-vertical horizontal">
                            <div class="bs-stepper-header mt-3">
                                <div class="step" data-target="#page-1">
                                    <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">1</span>
                                    <span class="bs-stepper-label mt-1">
                                        <span class="bs-stepper-title">Hasil Kegiatan</span>
                                        <span class="bs-stepper-subtitle">Isi Hasil Kegiatan</span>
                                    </span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                <div class="step" data-target="#page-2">
                                    <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">2</span>
                                    <span class="bs-stepper-label mt-1">
                                        <span class="bs-stepper-title">Evaluasi Kegiatan</span>
                                        <span class="bs-stepper-subtitle">Evaluasi dan Catatan Kegiatan</span>
                                    </span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                <div class="step" data-target="#page-3">
                                    <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">3</span>
                                    <span class="bs-stepper-label mt-1">
                                        <span class="bs-stepper-title">Realisasi Anggaran</span>
                                        <span class="bs-stepper-subtitle">Data Realisasi Anggaran</span>
                                    </span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                <div class="step" data-target="#page-4">
                                    <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">4</span>
                                    <span class="bs-stepper-label mt-1">
                                        <span class="bs-stepper-title">Lampiran <i>(Opsional)</i></span>
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

                            <div class="bs-stepper-content" id="wizard-container">
                            <form onSubmit="return false" id="form-laporan-proposal">
                                <input type="hidden" name="id_pro" id="id_pro" value="{{$id['id']}}">

                                <!-- Hasil Kegiatan -->
                                <div class="container">
                                    <div id="page-1" class="content mt-3">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                              <label for="hasil_kegiatan" class="form-label">Hasil Kegiatan</label>
                                              <div id="editor-hasil-kegiatan" class="mb-3" style="height: 300px;"></div>
                                              <textarea rows="3" class="mb-3 d-none" name="hasil_kegiatan" id="hasil_kegiatan"></textarea>
                                            </div>
                                            
                                            <div class="col-12 d-flex justify-content-between">
                                                <button class="btn btn-label-secondary btn-prev mt-3" disabled> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                                </button>
                                                <button class="btn btn-primary btn-next mt-3"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="bx bx-chevron-right bx-sm me-sm-n2"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Evaluasi Kegiatan -->
                                <div class="container">
                                    <div id="page-2" class="content mt-3">
                                        <div class="row g-3">
                                          <div class="col-md-6">
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
                                </div>

                                <!-- Rencana Anggaran -->
                                <div class="container">
                                    <div id="page-3" class="content mt-3">
                                        <div class="row g-3">
                                            <div class="divider divider-dashed text-start">
                                                <div class="divider-text">
                                                    <a class="text-info" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Klik untuk melihat data rencana anggaran </a>
                                                </div>
                                            </div>
                                            <div class="collapse" id="collapseExample">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Item</th>
                                                        <th>Biaya Satuan</th>
                                                        <th width="12%;">Qty</th>
                                                        <th width="12%;">Freq.</th>
                                                        <th>Total Biaya</th>
                                                        <th>Sumber Dana</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if($anggarans->count() > 0)
                                                        @foreach($anggarans as $no => $data)
                                                        <tr>
                                                            <td>{{++$no}}</td>
                                                            <td>{{$data->item}}</td>
                                                            <td>{{currency_IDR($data->biaya_satuan)}}</td>
                                                            <td>{{$data->quantity}}</td>
                                                            <td>{{$data->frequency}}</td>
                                                            @php $total = 0; $total = $data->biaya_satuan * $data->quantity * $data->frequency; @endphp
                                                            <td>{{currency_IDR($total)}}</td>
                                                            <td>@if($data->sumber_dana == '1') Kampus @else Mandiri @endif</td>
                                                        </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td colspan="5" style="text-align: right;"><b>Grand Total</b></td>
                                                            <td><b>{{currency_IDR($grandTotal['grandTotal'])}}</b></td>
                                                            <td></td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td colspan="7" style="text-align: center;">Tidak ada data rencana anggaran</td>
                                                        </tr>
                                                    @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                          <div class="divider text-start">
                                              <div class="divider-text">Masukkan Data Realisasi Anggaran</div>
                                          </div>
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
                                          <div class="col-12 d-flex justify-content-between">
                                            <button class="btn btn-primary btn-prev"> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                              <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                            </button>
                                            <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="bx bx-chevron-right bx-sm me-sm-n2"></i></button>
                                          </div>
                                        </div>
                                      </div>
                                </div>

                                <!-- Lampiran -->
                                <div class="container">
                                    <div id="page-4" class="content mt-3">
                                        <div class="col form-group p-0">
                                            <p style="font-size: 12px; line-height:12px;" class="text-primary mt-1">Note:<br>- Anda bisa menggunakan link google drive atau unggah berkas.<br>- Jika ingin unggah berupa berkas, silakan checklist upload berkas.<br></p>
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
                                            <p style="font-size: 14px;" class="text-info">**Silakan klik Next jika tidak ada data atau berkas yang ingin dilampirkan</p>
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
                                        <div class="row g-3">
                                          <div class="col-md-6">
                                            <label for="penutup" class="form-label">Penutup</label>
                                            <div id="editor-penutup" class="mb-3" style="height: 300px;"></div>
                                            <textarea class="mb-3 d-none" id="penutup" name="penutup" rows="5"></textarea>
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

    document.addEventListener("DOMContentLoaded", function () {
        const body = document.body;
        const wizardContainer = document.getElementById("wizard-container");

        // Saat wizard diaktifkan, tambahkan kelas no-scroll
        if (wizardContainer) {
            body.classList.add("no-scroll");
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
            const fileInputs = document.querySelectorAll('input[name^="berkas"]');
            const maxSize = 2 * 1024 * 1024; 
            let isValid = true;

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
                            url: "{{ route('insert-laporan-proposal') }}",
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
                                window.location = '{{ route("my-report") }}';
                            },
                            error: function (response) {
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
                                });
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

    // Editor Hasil Kegiatan
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