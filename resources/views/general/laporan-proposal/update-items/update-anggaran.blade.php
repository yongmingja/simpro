@extends('layouts.backend')
@section('title','Data Anggaran')

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
                            <a href="{{route('my-report')}}" class="dropdown-shortcuts-add text-body"><i class="bx bx-left-arrow-circle"></i> Back</a>&nbsp;&nbsp;
                            <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body" id="tombol-tambah"><button type="button" class="btn btn-primary"><i class="bx bx-plus-circle bx-spin-hover"></i> Add data</button></a>
                        </div>
                        <!-- AKHIR TOMBOL -->
                            <table class="table table-hover table-responsive" id="table-anggaran">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Nama Item</th>
                                  <th>Biaya Satuan</th>
                                  <th>Quantity</th>
                                  <th>Frequency</th>
                                  <th>Sumber dana</th>
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
                                                <label for="item" class="form-label">Nama Item</label>
                                                <input type="text" class="form-control" id="item" name="item" value="" placeholder="Masukkan item" />
                                                <span class="text-danger" id="itemErrorMsg" style="font-size: 10px;"></span>
                                            </div>                                          
                                            <div class="mb-3">
                                                <label for="biaya_satuan" class="form-label">Biaya Satuan</label>
                                                <input type="number" class="form-control" id="biaya_satuan" name="biaya_satuan" value="" placeholder="Masukkan jumlah biaya" />
                                                <span class="text-danger" id="biayaSatuanErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            <div class="mb-3">
                                                <label for="quantity" class="form-label">Quantity</label>
                                                <input type="number" class="form-control" id="quantity" name="quantity" value="" placeholder="Masukkan quantity" />
                                                <span class="text-danger" id="quantityErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            <div class="mb-3">
                                                <label for="frequency" class="form-label">Frequency</label>
                                                <input type="number" class="form-control" id="frequency" name="frequency" value="" placeholder="Masukkan frequency" />
                                                <span class="text-danger" id="frequencyErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            <div class="mb-3">
                                                <label for="sumber_dana" class="form-label">Sumber Dana</label>
                                                <select class="select2 form-select" name="sumber_dana" id="sumber_dana">
                                                    <option value="1">Kampus</option>
                                                    <option value="2">Mandiri</option>
                                                </select>
                                                <span class="text-danger" id="sumberSarprasErrorMsg" style="font-size: 10px;"></span>
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

                    <!-- modal edit anggaran-->
                    <div class="modal fade mt-3" tabindex="-1" role="dialog" id="editanggaran-modal" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-edit-anggaran">Edit Anggaran</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-edit-anggaran" name="form-edit-anggaran" class="form-horizontal">
                                        <input type="hidden" id="e_anggaran_id" name="e_anggaran_id" class="form-control">  
                                        <div class="mb-2">
                                            <label for="e_item" class="form-label">Item</label>
                                            <input type="text" class="form-control" id="e_item" name="e_item" value="" />
                                        </div>  
                                        <div class="mb-2">
                                            <label for="e_biaya_satuan" class="form-label">Biaya Satuan</label>
                                            <input type="number" min="0" class="form-control" id="e_biaya_satuan" name="e_biaya_satuan" value="" />
                                        </div>
                                        <div class="mb-2">
                                            <label for="e_quantity" class="form-label">Jumlah (Qty)</label>
                                            <input type="number" min="0" class="form-control" id="e_quantity" name="e_quantity" value="" />
                                        </div>   
                                        <div class="mb-2">
                                            <label for="e_frequency" class="form-label">Frekuensi</label>
                                            <input type="number" min="0" class="form-control" id="e_frequency" name="e_frequency" value="" />
                                        </div>   
                                        <div class="mb-2">
                                            <label for="e_sumber_dana" class="form-label">Sumber dana</label>
                                            <select class="select2 form-select" id="e_sumber_dana" name="e_sumber_dana" style="cursor:pointer;">
                                                <option value="1">Kampus</option>
                                                <option value="2">Mandiri</option>
                                              </select>
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
                    <!-- end of modal edit anggaran-->
                    
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
        var table = $('#table-anggaran').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('page-revisi-anggaran-laporan-proposal',['id' => $id]) }}",
                type: 'GET'
            },
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'item',name: 'item'},
                {data: 'biaya_satuan',name: 'biaya_satuan'},
                {data: 'quantity',name: 'quantity'},
                {data: 'frequency',name: 'frequency'},
                {data: 'sumber_dana',name: 'sumber_dana'},
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
                    url: "{{route('revisi-anggaran-laporan-proposal-store')}}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-tambah-edit').trigger("reset");
                        $('#tambah-edit-modal').modal('hide');
                        $('#tombol-simpan').html('Save');
                        $('#table-anggaran').DataTable().ajax.reload(null, true);
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
        var dataItem = $(this).data('item');
        var dataBiayaSatuan = $(this).data('biaya-satuan');
        var dataQuantity = $(this).data('quantity');
        var dataFrequency = $(this).data('frequency');
        var dataSumberDana = $(this).data('sumber-dana');
        $('#modal-judul-edit-anggaran').html("Edit item anggaran");
        $('#editanggaran-modal').modal('show');

        $('#e_anggaran_id').val(dataId);
        $('#e_item').val(dataItem);
        $('#e_biaya_satuan').val(dataBiayaSatuan);
        $('#e_quantity').val(dataQuantity);
        $('#e_frequency').val(dataFrequency);
        $('#e_sumber_dana').val(dataSumberDana);
    });

    if ($("#form-edit-anggaran").length > 0) {
        $("#form-edit-anggaran").validate({
            submitHandler: function (form) {
                var actionType = $('#btn-update').val();
                $('#btn-update').html('Updating..');

                $.ajax({
                    data: $('#form-edit-anggaran').serialize(),
                    url: "{{ route('edit-item-anggaran-laporan-proposal') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-edit-anggaran').trigger("reset");
                        $('#editanggaran-modal').modal('hide');
                        $('#btn-update').html('Update');
                        $('#table-anggaran').DataTable().ajax.reload(null, true);
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
                        url: "{{route('delete-item-anggaran-laporan-proposal')}}",
                        type: 'DELETE',
                        data: {id:dataId},
                        dataType: 'json'
                    }).done(function(response) {
                        $('#table-anggaran').DataTable().ajax.reload(null, true);
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