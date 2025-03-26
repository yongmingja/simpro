@extends('layouts.backend')
@section('title','Data Prodi & Biro')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('data-prodi-biro.index')}}">@yield('title')</a>
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
                            <table class="table table-hover table-responsive" id="table_prodi_biro">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Nama Prodi & Biro</th>
                                  <th>Nama Fakultas & Biro</th>
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
                                                <label for="nama_prodi_biro" class="form-label">Nama Prodi / Biro</label>
                                                <input type="text" class="form-control" id="nama_prodi_biro" name="nama_prodi_biro" value="" placeholder="e.g Program Studi Manajemen" autofocus />
                                                <span class="text-danger" id="namaProdiErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            <div class="mb-3">
                                                <label for="id_fakultas_biro" class="form-label">Fakultas / Biro</label>
                                                <select class="form-select" id="id_fakultas_biro" name="id_fakultas_biro" aria-label="Default select example" style="cursor:pointer;">
                                                    <option value="" id="pilih_fakultas">- Pilih -</option>
                                                    @foreach($getDataFakultasBiro as $fakultas_biro)
                                                    <option value="{{$fakultas_biro->id}}">{{$fakultas_biro->nama_fakultas_biro}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="idFakultasErrorMsg" style="font-size: 10px;"></span>
                                            </div>                                         
                                            <div class="mb-3">
                                                <label for="kode_prodi_biro" class="form-label">Kode Prodi / Biro</label>
                                                <input type="text" class="form-control" id="kode_prodi_biro" name="kode_prodi_biro" value="" placeholder="e.g MN" />
                                                <span class="text-danger" id="kodeProdiErrorMsg" style="font-size: 10px;"></span>
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
        var table = $('#table_prodi_biro').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('data-prodi-biro.index') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'nama_prodi_biro',name: 'nama_prodi_biro',
                    render: function(data, type, row, meta){
                        let text = row.nama_prodi_biro;
                        return text.replace('Program Studi','');
                    }
                },
                {data: 'nama_fakultas_biro',name: 'nama_fakultas_biro'},
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
                    url: "{{ route('data-prodi-biro.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-tambah-edit').trigger("reset");
                        $('#tambah-edit-modal').modal('hide');
                        $('#tombol-simpan').html('Save');
                        $('#table_prodi_biro').DataTable().ajax.reload(null, true);
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
                        $('#namaProdiErrorMsg').text(response.responseJSON.errors.nama_prodi_biro);
                        $('#idFakultasErrorMsg').text(response.responseJSON.errors.id_fakultas_biro);
                        $('#kodeProdiErrorMsg').text(response.responseJSON.errors.kode_prodi_biro);
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
        $.get('data-prodi-biro/' + data_id + '/edit', function (data) {
            $('#modal-judul').html("Edit data");
            $('#tombol-simpan').val("edit-post");
            $('#tambah-edit-modal').modal('show');
              
            $('#id').val(data.id);
            $('#nama_prodi_biro').val(data.nama_prodi_biro);
            $('#kode_prodi_biro').val(data.kode_prodi_biro);
            $('#id_fakultas_biro').val(data.id_fakultas_biro);
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
                        url: "data-prodi-biro/" + dataId,
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
                        $('#table_prodi_biro').DataTable().ajax.reload(null, true);
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

</script>

@endsection