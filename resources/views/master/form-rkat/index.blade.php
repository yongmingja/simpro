@extends('layouts.backend')
@section('title','Form RKAT')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('form-rkat.index')}}">@yield('title')</a>
      </li>
      <li class="breadcrumb-item active">Data</li>
    </ol>
</nav>
</div>
@endsection

@section('content')

<div class="container-fluid flex-grow-1">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card table-responsive">
                    <div class="card-body">
                        <!-- MULAI TOMBOL TAMBAH -->
                        <div class="mb-3">
                            <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body" id="tombol-tambah"><button type="button" class="btn btn-primary"><i class="bx bx-plus-circle bx-spin-hover"></i> New data</button></a>&nbsp;
                            <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body" id="click-csv"><button type="button" class="btn btn-secondary"><i class="bx bx-xs bx-import bx-tada-hover"></i> Import CSV</button></a>
                        </div>
                        
                        <!-- AKHIR TOMBOL -->
                            <table class="table table-hover table-responsive" id="table_rkat">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Tahun Akademik</th>
                                  <th>Fakultas / Biro</th>
                                  <th>Sasaran Strategi</th>
                                  <th>Program Strategis</th>
                                  <th>Kode Renstra</th>
                                  <th>Nama Kegiatan</th>
                                  <th>Kode Pagu</th>
                                  <th>Total</th>
                                  <th>Status</th>
                                  <th>Aksi</th>
                                </tr>
                              </thead>
                            </table>
                        </div>
                    </div>

                    <!-- MULAI MODAL FORM TAMBAH/EDIT-->
                    <div class="modal fade" id="tambah-edit-modal" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-tambah-edit" name="form-tambah-edit" class="form-horizontal">
                                        <div class="row">
                                            <input type="hidden" id="id" name="id">
                                            <div class="mb-3">
                                                <label for="id_fakultas_biro" class="form-label">Fakultas / Biro</label>
                                                <select class="select2 form-select" id="id_fakultas_biro" name="id_fakultas_biro" aria-label="Default select example" style="cursor:pointer;">
                                                    <option value="" id="pilih_fakultas">- Pilih -</option>
                                                    @foreach($getFakultasBiro as $fakultas_biro)
                                                    <option value="{{$fakultas_biro->id}}">{{$fakultas_biro->nama_fakultas_biro}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="dataFakultasBiroErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            <div class="mb-3">
                                                <label for="sasaran_strategi" class="form-label">Sasaran Strategi</label>
                                                <textarea class="form-control" id="sasaran_strategi" name="sasaran_strategi" rows="3"></textarea>
                                                <span class="text-danger" id="sasaranStrategiErrorMsg" style="font-size: 10px;"></span>
                                            </div>                                          
                                            <div class="mb-3">
                                                <label for="program_strategis" class="form-label">Program Strategis</label>
                                                <textarea class="form-control" id="program_strategis" name="program_strategis" rows="3"></textarea>
                                                <span class="text-danger" id="programStrategisErrorMsg" style="font-size: 10px;"></span>
                                            </div>                                          
                                            <div class="mb-3">
                                                <label for="program_kerja" class="form-label">Program Kerja</label>
                                                <textarea class="form-control" id="program_kerja" name="program_kerja" rows="3"></textarea>
                                                <span class="text-danger" id="programKerjaErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            <div class="mb-3">
                                                <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                                                <textarea class="form-control" id="nama_kegiatan" name="nama_kegiatan" rows="3"></textarea>
                                                <span class="text-danger" id="namaKegiatanErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            <div class="col-sm-12">
                                                <div class="row mb-2">
                                                    <div class="col-sm-6">
                                                        <label for="kode_renstra" class="form-label">Kode Renstra</label>
                                                        <input type="text" class="form-control" id="kode_renstra" name="kode_renstra" value="" />
                                                        <span class="text-danger" id="kodeRenstraErrorMsg" style="font-size: 10px;"></span>
                                                    </div>   
                                                    <div class="col-sm-6">
                                                        <label for="kode_pagu" class="form-label">Kode Pagu</label>
                                                        <input type="text" class="form-control" id="kode_pagu" name="kode_pagu" value="" />
                                                        <span class="text-danger" id="kodePaguErrorMsg" style="font-size: 10px;"></span>
                                                    </div>
                                                </div> 
                                            </div>    
                                            <div class="mb-3">
                                                <label for="total" class="form-label">Total Anggaran</label>
                                                <input type="text" class="form-control" id="total" name="total" value="" />
                                                <span class="text-danger" id="totalErrorMsg" style="font-size: 10px;"></span>
                                            </div>     
                                            
                                            <div class="col-sm-offset-2 col-sm-12">
                                                <hr class="mt-2">
                                                <div class="float-sm-end">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary btn-block" id="tombol-simpan" value="create">Save</button>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- AKHIR MODAL -->

                    <!-- Modal Import data RKAT -->
                    <div class="modal fade" id="import-data-rkat" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-import"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="card border">
                                        <div class="card-body">
                                            <p><i class="bx bx-spa"></i> Sebelum anda import data RKAT berikut sedikit petunjuk:<br>
                                            <li>Anda bisa mengunduh terlebih dahulu format berkas CSV <a href="{{ asset('template/data-rkat-upt-si.csv')}}" target="_blank">di sini</a>.<br>
                                            <li>Pilihlah Fakultas, unit atau biro yang ingin anda import.<br>
                                            <li>Pilihlah file berekstensi .csv pada komputer anda.<br>
                                            <li>Langkah terakhir adalah klik tombol Import. Selesai!
                                            </p>
                                        </div>
                                    </div>
                                    <form id="form-import-csv" name="form-import-csv" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row mt-3">
                                            <div class="mb-3">
                                                <label for="pilih_fakultas_biro" class="form-label">Fakultas / Biro</label>
                                                <select class="select2 form-select" id="pilih_fakultas_biro" name="pilih_fakultas_biro" aria-label="Default select example" style="cursor:pointer;">
                                                    <option value="" id="pilih_fakultas_biro">- Pilih -</option>
                                                    @foreach($getFakultasBiro as $fakultas_biro)
                                                    <option value="{{$fakultas_biro->id}}">{{$fakultas_biro->nama_fakultas_biro}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="pilihFakultasBiroErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            <div class="mb-3">
                                                <label for="file_csv" class="form-label">File Excel/CSV</label>
                                                <div class="form-group mt-1 mb-1">
                                                    <input id="file_csv" type="file" name="file_csv" accept=".csv" data-preview-file-type="any" class="file form-control" required data-upload-url="#">
                                                </div>
                                                <span class="text-danger" id="fileErrorMsg"></span>
                                            </div>                                          
                                            
                                            <div class="col-sm-offset-2 col-sm-12">
                                                <hr class="mt-2">
                                                <div class="float-sm-end">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary btn-block" id="tombol-import" value="import">Import</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End import data rkat -->
                    
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
@section('script')

<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    // DATATABLE
    $(document).ready(function () {
        var table = $('#table_rkat').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('form-rkat.index') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'year',name: 'year'},
                {data: 'nama_fakultas_biro',name: 'nama_fakultas_biro'},
                {data: 'sasaran_strategi',name: 'sasaran_strategi'},
                {data: 'program_strategis',name: 'program_strategis'},
                {data: 'kode_renstra',name: 'kode_renstra'},
                {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                {data: 'kode_pagu',name: 'kode_pagu'},
                {data: 'total',  render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' )},
                {data: 'status',name: 'status'},
                {data: 'action',name: 'action'},
            ]
        });
    });

    //TOMBOL TAMBAH DATA
    $('#tombol-tambah').click(function () {
        $('#button-simpan').val("create-post");
        $('#id').val('');
        $('#form-tambah-edit').trigger("reset");
        $('#modal-judul').html("Add new data");
        $('#tambah-edit-modal').modal('show');
    });

    // TOMBOL TAMBAH
    if ($("#form-tambah-edit").length > 0) {
        $("#form-tambah-edit").validate({
            submitHandler: function (form) {
                var actionType = $('#tombol-simpan').val();
                $('#tombol-simpan').html('Saving..');

                $.ajax({
                    data: $('#form-tambah-edit').serialize(), 
                    url: "{{ route('form-rkat.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-tambah-edit').trigger("reset");
                        $('#tambah-edit-modal').modal('hide');
                        $('#tombol-simpan').html('Save');
                        $('#table_rkat').DataTable().ajax.reload(null, true);
                        Swal.fire({
                            title: 'Good job!',
                            text: 'Data saved successfully!',
                            type: 'success',
                            customClass: {
                            confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false,
                            timer: 2000
                        })
                    },
                    error: function(response) {
                        $('#dataFakultasBiroErrorMsg').text(response.responseJSON.errors.id_fakultas_biro);
                        $('#sasaranStrategiErrorMsg').text(response.responseJSON.errors.sasaran_strategi);
                        $('#programStrategisErrorMsg').text(response.responseJSON.errors.program_strategis);
                        $('#programKerjaErrorMsg').text(response.responseJSON.errors.program_kerja);
                        $('#kodeRenstraErrorMsg').text(response.responseJSON.errors.kode_renstra);
                        $('#namaKegiatanErrorMsg').text(response.responseJSON.errors.nama_kegiatan);
                        $('#kodePaguErrorMsg').text(response.responseJSON.errors.kode_pagu);
                        $('#totalErrorMsg').text(response.responseJSON.errors.total);
                        $('#tombol-simpan').html('Save');
                        Swal.fire({
                            title: 'Error!',
                            text: 'Data failed to save!',
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

    // EDIT DATA
    $('body').on('click', '.edit-post', function () {
        var data_id = $(this).data('id');
        $.get('form-rkat/' + data_id + '/edit', function (data) {
            $('#modal-judul').html("Edit data");
            $('#tombol-simpan').val("edit-post");
            $('#tambah-edit-modal').modal('show');
              
            $('#id').val(data.id);
            $('#id_tahun_akademik').val(data.id_tahun_akademik);
            $('#sasaran_strategi').val(data.sasaran_strategi);
            $('#program_strategis').val(data.program_strategis);
            $('#program_kerja').val(data.program_kerja);
            $('#kode_renstra').val(data.kode_renstra);
            $('#nama_kegiatan').val(data.nama_kegiatan);
            $('#kode_pagu').val(data.kode_pagu);
            $('#total').val(data.total);
        })
    });

    // TOMBOL DELETE
    $(document).on('click', '.delete', function () {
        dataId = $(this).attr('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "It will be deleted permanently!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            showLoaderOnConfirm: true,
            preConfirm: function() {
                return new Promise(function(resolve) {
                    $.ajax({
                        url: "form-rkat/" + dataId,
                        type: 'DELETE',
                        data: {id:dataId},
                        dataType: 'json'
                    }).done(function(response) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Your data has been deleted.',
                            type: 'success',
                            timer: 2000
                        })
                        $('#table_rkat').DataTable().ajax.reload(null, true);
                    }).fail(function() {
                        Swal.fire({
                            title: 'Oops!',
                            text: 'Something went wrong!',
                            type: 'error',
                            timer: 2000
                        })
                    });
                });
            },
        });
    });

    // INPUT FORMAT RUPIAH OTOMATIS 
    var rupiah = document.getElementById('total');
    rupiah.addEventListener('keyup',function(e){
    rupiah.value = formatRupiah(this.value,'Rp. ');
    })

    function formatRupiah(angka, prefix){
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if(ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }

    $('#click-csv').click(function(){
        $('#button-simpan').val("create-post");
        $('#form-import-csv').trigger("reset");
        $('#modal-judul-import').html("Import Data RKAT");
        $('#import-data-rkat').modal('show');
    });

    if ($("#form-import-csv").length > 0) {
        $("#form-import-csv").validate({
            submitHandler: function (form) {
                var actionType = $('#tombol-import').val();
                var formData = new FormData($("#form-import-csv")[0]);
                $('#tombol-import').html('Importing..');

                $.ajax({
                    data: formData,
                    contentType: false,
                    processData: false,
                    url: "{{ route('import-data-rkat') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-import-csv').trigger("reset");
                        $('#import-data-rkat').modal('hide');
                        $('#tombol-import').html('Import');
                        $('#table_rkat').DataTable().ajax.reload(null, true);
                        Swal.fire({
                            title: 'Good job!',
                            text: 'Data imported successfully!',
                            type: 'success',
                            customClass: {
                            confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false,
                            timer: 2000
                        })
                    },
                    error: function(response) {
                        $('#fileErrorMsg').text(response.responseJSON.errors.file_csv);
                        $('#tombol-import').html('Import');
                        Swal.fire({
                            title: 'Error!',
                            text: 'Data failed to import!',
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

</script>

@endsection