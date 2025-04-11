@extends('layouts.backend')
@section('title','Data Sarpras')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="javascript:void()">@yield('title')</a>
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
                            <a href="{{route('submission-of-proposal.index')}}" class="dropdown-shortcuts-add text-body"><i class="bx bx-left-arrow-circle"></i> Back</a>&nbsp;&nbsp;
                            <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body" id="tombol-tambah"><button type="button" class="btn btn-primary"><i class="bx bx-plus-circle bx-spin-hover"></i> Add data</button></a>
                        </div>
                        <!-- AKHIR TOMBOL -->
                            <table class="table table-hover table-responsive" id="table-sarpras">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Tgl Kegiatan</th>
                                  <th>Item Sarpras</th>
                                  <th>Jumlah</th>
                                  <th>Sumber dana</th>
                                  <th>Keterangan</th>
                                  <th>Status</th>
                                  <th>Alasan Ditolak</th>
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
                                        <input type="hidden" id="id_proposal" name="id_proposal" value="{{$id}}">
                                        <div class="row">
                                            <div class="mb-3">
                                                <label for="tgl_kegiatan" class="form-label">Tanggal Kegiatan</label>
                                                <input type="date" class="form-control" id="tgl_kegiatan" name="tgl_kegiatan" value="" placeholder="mm/dd/yyyy"/>
                                                <span class="text-danger" id="tglKegiatanErrorMsg" style="font-size: 10px;"></span>
                                            </div>                                          
                                            <div class="mb-3">
                                                <label for="sarpras_item" class="form-label">Item Sarpras</label>
                                                <input type="text" class="form-control" id="sarpras_item" name="sarpras_item" value="" placeholder="Masukkan sarpras" />
                                                <span class="text-danger" id="sarprasItemErrorMsg" style="font-size: 10px;"></span>
                                            </div>                                          
                                            <div class="mb-3">
                                                <label for="jumlah" class="form-label">Jumlah</label>
                                                <input type="number" class="form-control" id="jumlah" name="jumlah" value="" placeholder="Masukkan jumlah yang dibutuhkan" />
                                                <span class="text-danger" id="jumlahErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            <div class="mb-3">
                                                <label for="sumber_dana" class="form-label">Sumber Sarpras</label>
                                                <select class="select2 form-select" name="sumber_dana" id="sumber_dana">
                                                    <option value="1">Kampus</option>
                                                    <option value="2">Mandiri</option>
                                                </select>
                                                <span class="text-danger" id="sumberSarprasErrorMsg" style="font-size: 10px;"></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="keterangan" class="form-label">Keterangan Sarpras</label>
                                                <input type="text" class="form-control" id="keterangan" name="keterangan" value="" placeholder="Masukkan keterangan sarpras" />
                                                <span class="text-danger" id="keteranganErrorMsg" style="font-size: 10px;"></span>
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

                    <!-- EDIT ITEM Sarpras -->
                    <div class="modal fade mt-3" tabindex="-1" role="dialog" id="pushedit-modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-edit">Edit Item Sarpras</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-edit-sarpras" name="form-edit-sarpras" class="form-horizontal">
                                        <input type="hidden" id="e_sarpras_id" name="e_sarpras_id" class="form-control">
                                        <div class="col-sm-12">
                                            <div class="col-sm-4 mb-2">
                                                <label for="e_tgl_kegiatan" class="form-label">Tgl Kegiatan</label>
                                                <input type="date" class="form-control" id="e_tgl_kegiatan" name="e_tgl_kegiatan" value="" placeholder="mm/dd/yyyy" />
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label for="e_sarpras_item" class="form-label">Item</label>
                                            <textarea class="form-control" id="e_sarpras_item" name="e_sarpras_item" rows="4"></textarea>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <div class="col-sm-6 mb-2">
                                                    <label for="e_jumlah" class="form-label">Jumlah</label>
                                                    <input type="number" class="form-control" id="e_jumlah" name="e_jumlah" value="" />
                                                </div>                                            
                                                <div class="col-sm-6 mb-2">
                                                    <label for="e_sumber" class="form-label">Sumber dana</label>
                                                    <select class="select2 form-select" id="e_sumber" name="e_sumber" style="cursor:pointer;">
                                                        <option value="1">Kampus</option>
                                                        <option value="2">Mandiri</option>
                                                      </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="e_keterangan" class="form-label">Keterangan Sarpras</label>
                                            <input type="text" class="form-control" id="e_keterangan" name="e_keterangan" value="" placeholder="Masukkan keterangan sarpras" />
                                            <span class="text-danger" id="keteranganErrorMsg" style="font-size: 10px;"></span>
                                        </div> 
                                        <div class="col-sm-offset-2 col-sm-12">
                                            <hr class="mt-2">
                                            <div class="float-sm-end">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary btn-block" id="btn-update" value="create">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- EOF Edit Item Sarpras -->
                    
                </div>
            </div>
        </div>
    </section>
</div>
<div>
    <input type="hidden" value="{{ $id }}">
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
        var table = $('#table-sarpras').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('page-update-sarpras',['id' => $id]) }}",
                type: 'GET'
            },
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'tgl_kegiatan',name: 'tgl_kegiatan',
                    render: function ( data, type, row ){
                        return moment(row.tgl_kegiatan).format("DD MMM YYYY")
                    }
                },
                {data: 'sarpras_item',name: 'sarpras_item'},
                {data: 'jumlah',name: 'jumlah'},
                {data: 'sumber_dana',name: 'sumber_dana'},
                {data: 'keterangan',name: 'keterangan'},
                {data: 'status',name: 'status'},
                {data: 'alasan',name: 'alasan'},
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
        $('#catchId').data(id);
    });

    // TOMBOL TAMBAH
    if ($("#form-tambah-edit").length > 0) {
        $("#form-tambah-edit").validate({
            submitHandler: function (form) {
                var actionType = $('#tombol-simpan').val();
                $('#tombol-simpan').html('Saving..');
                $.ajax({
                    data: $('#form-tambah-edit').serialize(), 
                    url: "{{route('update-sarpras-store')}}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-tambah-edit').trigger("reset");
                        $('#tambah-edit-modal').modal('hide');
                        $('#tombol-simpan').html('Save');
                        $('#table-sarpras').DataTable().ajax.reload(null, true);
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
                        $('#tglKegiatanErrorMsg').text(response.responseJSON.errors.tgl_kegiatan);
                        $('#sarprasItemErrorMsg').text(response.responseJSON.errors.sarpras_item);
                        $('#jumlahErrorMsg').text(response.responseJSON.errors.jumlah);
                        $('#sumberSarprasErrorMsg').text(response.responseJSON.errors.sumber_dana);
                        $('#keteranganErrorMsg').text(response.responseJSON.errors.keterangan);
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
        var dataTgl = $(this).data('tgl');
        var dataItem = $(this).data('item');
        var dataJumlah = $(this).data('jumlah');
        var dataSumber = $(this).data('sumber');
        var dataKeterangan = $(this).data('keterangan');
        $('#modal-judul-edit').html("Edit item sarpras");
        $('#pushedit-modal').modal('show');

        $('#e_sarpras_id').val(dataId);
        $('#e_tgl_kegiatan').val(dataTgl);
        $('#e_sarpras_item').val(dataItem);
        $('#e_jumlah').val(dataJumlah);
        $('#e_sumber_dana').val(dataSumber);
        $('#e_keterangan').val(dataKeterangan);
    });

    if ($("#form-edit-sarpras").length > 0) {
        $("#form-edit-sarpras").validate({
            submitHandler: function (form) {
                var actionType = $('#btn-update').val();
                $('#btn-update').html('Updating..');

                $.ajax({
                    data: $('#form-edit-sarpras').serialize(),
                    url: "{{ route('edit-item-sarpras') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-edit-sarpras').trigger("reset");
                        $('#pushedit-modal').modal('hide');
                        $('#btn-update').html('Update');
                        $('#table-sarpras').DataTable().ajax.reload(null, true);
                        Swal.fire({
                            title: 'Good job!',
                            text: 'Data updated successfully!',
                            type: 'success',
                            customClass: {
                            confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false,
                            timer: 2000
                        })
                    },
                    error: function(response) {
                        $('#btn-update').html('Update');
                        Swal.fire({
                            title: 'Error!',
                            text: 'Data failed to update!',
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

    // TOMBOL DELETE ITEM SARPRAS
    $(document).on('click', '.delete-post', function () {
        dataId = $(this).data('id');
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
                        url: "{{route('delete-item-sarpras')}}",
                        type: 'DELETE',
                        data: {id:dataId},
                        dataType: 'json'
                    }).done(function(response) {
                        $('#table-sarpras').DataTable().ajax.reload(null, true);
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Your data has been deleted.',
                            type: 'success',
                            timer: 2000
                        })
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