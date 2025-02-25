@extends('layouts.backend')
@section('title','Data Jabatan Pegawai')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('data-jabatan-pegawai.index')}}">@yield('title')</a>
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
                            <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body" id="tombol-tambah"><button type="button" class="btn btn-primary"><i class="bx bx-plus-circle bx-spin-hover"></i> New data</button></a>
                        </div>
                        
                        <!-- AKHIR TOMBOL -->
                            <table class="table table-hover table-responsive" id="table_jabatan">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Pegawai</th>
                                  <th>NIP / User ID</th>
                                  <th>Jabatan</th>
                                  <th>Ket Jabatan</th>
                                  <th>Actions</th>
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
                                                <label for="id_pegawai" class="form-label">Pegawai</label>
                                                <select class="select2 form-select" id="id_pegawai" name="id_pegawai" aria-label="Default select example" style="cursor:pointer;">
                                                    <option value="" id="pilih_pegawai">- Pilih -</option>
                                                    @foreach($getPegawai as $pegawai)
                                                    <option value="{{$pegawai->id}}">{{$pegawai->nama_pegawai}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="idPegawaiErrorMsg" style="font-size: 10px;"></span>
                                            </div> 

                                            <div class="mb-3">
                                                <label for="id_jabatan" class="form-label">Jabatan</label>
                                                <select class="select2 form-select" id="id_jabatan" name="id_jabatan" aria-label="Default select example" style="cursor:pointer;" onchange="display()">
                                                    <option value="" id="pilih_jabatan">- Pilih -</option>
                                                    @foreach($getJabatan as $jabatan)
                                                    <option value="{{$jabatan->id}}">{{$jabatan->nama_jabatan}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="idJabatanErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            <div id="showForm" name="showForm" class="mb-1"></div>
                                            
                                            
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

                    <!-- Mulai modal lihat jabatan -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="show_jabatan" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">User: Jabatan Pegawai</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table_show_jabatan" class="col-sm-12 table-responsive mb-3"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of modal lihat jabatan-->
                    
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
        var table = $('#table_jabatan').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('data-jabatan-pegawai.index') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'nama_pegawai',name: 'nama_pegawai'},
                {data: 'user_id',name: 'user_id'},
                {data: 'jabatan_nama',name: 'jabatan_nama'},
                {data: 'ket_jabatan',name: 'ket_jabatan'},
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
                    url: "{{ route('data-jabatan-pegawai.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-tambah-edit').trigger("reset");
                        $('#tambah-edit-modal').modal('hide');
                        $('#tombol-simpan').html('Save');
                        $('#table_jabatan').DataTable().ajax.reload(null, true);
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
                        $('#idPegawaiErrorMsg').text(response.responseJSON.errors.id_pegawai);
                        $('#idJabatanErrorMsg').text(response.responseJSON.errors.id_jabatan);
                        $('#ketJabatanErrorMsg').text(response.responseJSON.errors.ket_jabatan);
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
        var dataId = $(this).data('id');
        $.ajax({
            url: "{{route('check-jabatan-pegawai')}}",
            method: "GET",
            data: {user_id: dataId},
            success: function(response, data){
                $('#show_jabatan').modal('show');
                $("#table_show_jabatan").html(response.card)
            }
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
                        url: "data-jabatan-pegawai/" + dataId,
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
                        $('#table_jabatan').DataTable().ajax.reload(null, true);
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

    /* using JavaScript */
    function display(){
        var jabatanSelected = $('#id_jabatan').val();
        if(jabatanSelected == 6){ // Static id, Dekan atau KaBiro
        document.getElementById("showForm").innerHTML='<div class="mb-3"><label for="id_fakultas_biro" class="form-label">Fakultas / Biro *(Jika Jabatan Dekan atau Ka.Biro)</label><select class="select2 form-select" id="id_fakultas_biro" name="id_fakultas_biro" aria-label="Default select example" style="cursor:pointer;"><option value="" id="pilih_fakultas">- Pilih -</option>@foreach($getFakultasBiro as $fakultas_biro)<option value="{{$fakultas_biro->id}}">{{$fakultas_biro->nama_fakultas_biro}}</option>@endforeach</select><span class="text-danger" id="idJabatanErrorMsg" style="font-size: 10px;"></span></div> <div class="mb-3"><label for="ket_jabatan" class="form-label">Keterangan Jabatan</label><input type="text" class="form-control" id="ket_jabatan" name="ket_jabatan" value="" /><span class="text-danger" id="ketJabatanErrorMsg" style="font-size: 10px;"></span></div>';
        } else {
        document.getElementById("showForm").innerHTML='<div class="mb-3"><label for="ket_jabatan" class="form-label">Keterangan Jabatan</label><input type="text" class="form-control" id="ket_jabatan" name="ket_jabatan" value="" /><span class="text-danger" id="ketJabatanErrorMsg" style="font-size: 10px;"></span></div>';
        }
    }

</script>

@endsection