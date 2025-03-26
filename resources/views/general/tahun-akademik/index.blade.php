@extends('layouts.backend')
@section('title','Tahun Akademik')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('tahun-akademik.index')}}">@yield('title')</a>
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
                            <table class="table table-hover table-responsive" id="table_tahun_akademik">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Tahun</th>
                                  <th>Tanggal Mulai</th>
                                  <th>Tanggal Akhir</th>
                                  <th>Status</th>
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
                                                <label for="year" class="form-label">Tahun</label>
                                                <input type="text" class="form-control" id="year" name="year" value="" autofocus />
                                                <span class="text-danger" id="yearErrorMsg" style="font-size: 10px;"></span>
                                            </div>                                          
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                                <input type="date" class="form-control" id="start_date" name="start_date" value="" />
                                                <span class="text-danger" id="startDateErrorMsg" style="font-size: 10px;"></span>
                                            </div>                                          
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date" value="" />
                                                <span class="text-danger" id="endDateErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            <label for="is_active" class="form-label mb-2">Status:</label>
                                            <div class="mb-2">
                                                <label class="switch switch-primary">
                                                    <span class="switch-label">Tidak Aktif</span>
                                                    <input type="checkbox" class="switch-input" value="1" id="is_active" name="is_active" />
                                                    <span class="switch-toggle-slider">
                                                      <span class="switch-on">
                                                        <i class="bx bx-check"></i>
                                                      </span>
                                                      <span class="switch-off">
                                                        <i class="bx bx-x"></i>
                                                      </span>
                                                    </span>
                                                    <span class="switch-label">Aktif</span>
                                                  </label>
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
        var table = $('#table_tahun_akademik').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('tahun-akademik.index') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'year',name: 'year'},
                {data: 'start_date',name: 'start_date'},
                {data: 'end_date',name: 'end_date'},
                {data: 'state',name: 'state'},
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
                    url: "{{ route('tahun-akademik.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-tambah-edit').trigger("reset");
                        $('#tambah-edit-modal').modal('hide');
                        $('#tombol-simpan').html('Save');
                        $('#table_tahun_akademik').DataTable().ajax.reload(null, true);
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
                        $('#yearErrorMsg').text(response.responseJSON.errors.year);
                        $('#startDateErrorMsg').text(response.responseJSON.errors.start_date);
                        $('#endDateErrorMsg').text(response.responseJSON.errors.end_date);
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
        $.get('tahun-akademik/' + data_id + '/edit', function (data) {
            $('#modal-judul').html("Edit data");
            $('#tombol-simpan').val("edit-post");
            $('#tambah-edit-modal').modal('show');
              
            $('#id').val(data.id);
            $('#year').val(data.year);
            $('#start_date').val(data.start_date);
            $('#end_date').val(data.end_date);
            $('#is_active').val(data.is_active);
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
                        url: "tahun-akademik/" + dataId,
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
                        $('#table_tahun_akademik').DataTable().ajax.reload(null, true);
                    }).fail(function() {
                        Swal.fire({
                            title: 'Oops!',
                            text: 'Data failed to remove!',
                            type: 'error',
                            timer: 2000
                        })
                    });
                });
            },
        });
    });

    /* UNTUK TOGGLE STATUS */
    function PeriodeStatus(id,is_active){
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ Route('change-period-status') }}",
            id: $('.period-status'+id+'').val(),
            data:{'is_active':is_active,'id':id},
        }).done(function(data, response) {
            Swal.fire({
                title: 'Success!',
                text: 'State changed successfully!',
                type: 'success',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false,
                timer: 2000
            })
            $('#table_tahun_akademik').DataTable().ajax.reload(null, true);
        })
    }

</script>

@endsection