@extends('layouts.backend')
@section('title','Data Pegawai')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('data-pegawai.index')}}">@yield('title')</a>
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
                            <table class="table table-hover table-responsive" id="table_pegawai">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Nama Pegawai</th>
                                  <th>N I P</th>
                                  <th>Email</th>
                                  <th>Reset Password</th>
                                  <th>Aksi</th>
                                </tr>
                              </thead>
                            </table>

                        </div>
                    </div>

                    <!-- MULAI MODAL FORM TAMBAH/EDIT-->
                    <div class="modal fade" id="tambah-edit-modal" aria-hidden="true">
                        <div class="modal-dialog ">
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
                                                <label for="user_id" class="form-label">N I P*</i></label>
                                                <input type="text" class="form-control" id="user_id" name="user_id" value="" />
                                                <div class="text-muted mt-1" style="font-size: 10px; font-style:italic;">Note: Ditulis lengkap dengan titik (contoh: 2011.6.900)</div>
                                                <span class="text-danger" id="nipErrorMsg" style="font-size: 10px;"></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="nama_pegawai" class="form-label">Nama Lengkap <i class="text muted">(beserta gelar)</i></label>
                                                <input type="text" class="form-control" id="nama_pegawai" name="nama_pegawai" value="" />
                                                <span class="text-danger" id="nameErrorMsg" style="font-size: 10px;"></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" value="" placeholder="type an active email" />
                                                <span class="text-danger" id="emailErrorMsg" style="font-size: 10px;"></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Password</label>
                                                <input type="password" class="form-control" id="password" name="password" value="" />
                                                <span class="text-danger" id="passwordErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            
                                            <div class="mb-3">
                                                <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                                                <select class="select2 form-control border border-primary" id="jenis_kelamin" name="jenis_kelamin" aria-label="Default select example" style="cursor:pointer;">
                                                    <option option value="" id="choose_jenis_kelamin" readonly>- Select -</option>
                                                    <option value="L">Laki-laki</option>
                                                    <option value="P">Perempuan</option>
                                                </select>
                                                <span class="text-danger" id="jenisKelaminErrorMsg" style="font-size: 10px;"></span>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="agama">Agama</label>
                                                <select class="select2 form-control border border-primary" id="agama" name="agama" aria-label="Default select example" style="cursor:pointer;">
                                                    <option option value="" id="choose_agama" readonly>- Select -</option>
                                                    <option value="Buddha">Buddha</option>
                                                    <option value="Buddha Maitreya">Buddha Maitreya</option>
                                                    <option value="Islam">Islam</option>
                                                    <option value="Katholik">Katholik</option>
                                                    <option value="Kristen">Kristen</option>
                                                    <option value="Konghucu">Konghucu</option>
                                                    <option value="Hindu">Hindu</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                                <span class="text-danger" id="agamaErrorMsg" style="font-size: 10px;"></span>
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

                    <div class="modal fade" id="import-pegawai" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-import"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-import-csv" name="form-import-csv" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="mb-3">
                                                <label for="file_csv" class="form-label">File Excel/CSV</label>
                                                <div class="form-group mt-1 mb-1">
                                                    <input id="file_csv" type="file" name="file_csv" data-preview-file-type="any" class="file form-control" required data-upload-url="#">
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
        var table = $('#table_pegawai').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('data-pegawai.index') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'nama_pegawai',name: 'nama_pegawai'},
                {data: 'user_id',name: 'user_id'},
                {data: 'email',name: 'email'},
                {data: 'reset-pass',name: 'reset-pass'},
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
                    url: "{{ route('data-pegawai.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-tambah-edit').trigger("reset");
                        $('#tambah-edit-modal').modal('hide');
                        $('#tombol-simpan').html('Save');
                        $('#table_pegawai').DataTable().ajax.reload(null, true);
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
                        $('#nipErrorMsg').text(response.responseJSON.errors.user_id);
                        $('#nameErrorMsg').text(response.responseJSON.errors.nama_pegawai);
                        $('#emailErrorMsg').text(response.responseJSON.errors.email);
                        $('#passwordErrorMsg').text(response.responseJSON.errors.password);
                        $('#jenisKelaminErrorMsg').text(response.responseJSON.errors.jenis_kelamin);
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
        $.get('data-pegawai/' + data_id + '/edit', function (data) {
            $('#modal-judul').html("Edit data");
            $('#tombol-simpan').val("edit-post");
            $('#tambah-edit-modal').modal('show');
              
            $('#id').val(data.id);
            $('#user_id').val(data.user_id);
            $('#nama_pegawai').val(data.nama_pegawai);
            $('#email').val(data.email);
            $('#jenis_kelamin').val(data.jenis_kelamin);
            $('#agama').val(data.agama);
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
                        url: "data-pegawai/" + dataId,
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
                        $('#table_pegawai').DataTable().ajax.reload(null, true);
                    }).fail(function() {
                        Swal.fire({
                            title: 'Oops!',
                            text: 'Something went wrong with ajax!',
                            type: 'error',
                            timer: 2000
                        })
                    });
                });
            },
        });
    });

    $('#click-csv').click(function(){
        $('#button-simpan').val("create-post");
        $('#form-import-csv').trigger("reset");
        $('#modal-judul-import').html("Import Pegawai");
        $('#import-pegawai').modal('show');
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
                    url: "{{ route('import-data-pegawai') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-import-csv').trigger("reset");
                        $('#import-pegawai').modal('hide');
                        $('#tombol-import').html('Import');
                        $('#table_pegawai').DataTable().ajax.reload(null, true);
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
    $('body').on('click','.reset-pass', function(){
        var data_id = $(this).attr('id');
        $.ajax({
            url: "{{ route('reset-pass-pegawai')}}",
            type: "POST",
            data: {
                user_id:data_id
            },
            dataType: 'json',
            success: function (data) {
                $('#table_pegawai').DataTable().ajax.reload(null, true);
                Swal.fire({
                    title: 'Good job!',
                    text: 'Password reset successfully!',
                    type: 'success',
                    customClass: {
                    confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false,
                    timer: 2000
                })
            },
        })
    })

</script>

@endsection