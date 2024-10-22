@extends('layouts.backend')
@section('title','Ajukan Proposal')

@section('breadcrumbs')
<div class="container-xxl">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="javascript:void(0)">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('submission-of-proposal.index')}}">@yield('title')</a>
      </li>
      <li class="breadcrumb-item active">Data</li>
    </ol>
</nav>
</div>
@endsection
<style>
    .horizontal-timeline .items {
    border-top: 3px solid #e9ecef;
    }

    .horizontal-timeline .items .items-list {
    display: block;
    position: relative;
    text-align: center;
    padding-top: 70px;
    margin-right: 0;
    }

    .horizontal-timeline .items .items-list:before {
    content: "";
    position: absolute;
    height: 36px;
    border-right: 2px dashed #dee2e6;
    top: 0;
    }

    .horizontal-timeline .items .items-list .event-date {
    position: absolute;
    top: 36px;
    left: 0;
    right: 0;
    width: 90px;
    margin: 0 auto;
    font-size: 0.7rem;
    padding-top: 8px;
    }

    @media (min-width: 1140px) {
    .horizontal-timeline .items .items-list {
        display: inline-block;
        width: 24%;
        padding-top: 45px;
    }

    .horizontal-timeline .items .items-list .event-date {
        top: -40px;
    }
    }
</style>

@section('content')

<div class="container-xxl flex-grow-1">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card table-responsive">
                    <div class="card-body">
                        <!-- MULAI TOMBOL TAMBAH -->
                        @if($checkLap->count() > 0)
                            @foreach($checkLap as $p)  @endforeach
                                @if($p->status_laporan == '')
                                    <div class="mb-3">
                                        <a href="javascript:void(0)" class="dropdown-shortcuts-add text-muted"><button type="button" class="btn btn-outline-secondary" onclick="alert('Anda dapat mengajukan proposal baru setelah menyelesaikan laporan pertanggung-jawaban proposal Anda sebelumnya dan telah di verifikasi oleh Rektorat! Mohon periksa kembali status proposal Anda.')"><i class="bx bx-plus-circle bx-spin-hover"></i> Proposal Baru</button></a>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <a href="{{route('tampilan-proposal-baru')}}" class="dropdown-shortcuts-add text-body" id="proposal-baru"><button type="button" class="btn btn-outline-primary"><i class="bx bx-plus-circle bx-spin-hover"></i> Proposal Baru</button></a>
                                    </div>
                                @endif
                           
                        @else
                            <div class="mb-3">
                                <a href="{{route('tampilan-proposal-baru')}}" class="dropdown-shortcuts-add text-body" id="proposal-baru"><button type="button" class="btn btn-outline-primary"><i class="bx bx-plus-circle bx-spin-hover"></i> Proposal Baru</button></a>
                            </div>
                        @endif
                        <!-- AKHIR TOMBOL -->
                        <table class="table table-hover table-responsive" id="table_proposal">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Preview</th>
                                <th>Proposal</th>
                                <th>Nama Kegiatan</th>
                                <th>Nama Pengaju</th>
                                <th width="12%;">Aksi</th>
                                <th width="12%;">Status</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    
                     <!-- Mulai modal detail -->
                     <div class="modal fade" tabindex="-1" role="dialog" id="show-detail" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">Progres Timeline Proposal</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table" class="col-sm-12 table-responsive mb-3"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of modal detail-->

                    {{-- Info detil keterangan --}}
                    <div class="modal animate__animated animate__swing mt-3" tabindex="-1" role="dialog" id="keterangan-modal" aria-hidden="true">
                        <div class="modal-dialog ">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-ket"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="mb-3">
                                            <textarea class="form-control" id="detil_ket" name="detil_ket" rows="10" readonly></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Info detil keterangan --}}

                    <div class="modal animate__animated animate__swing mt-3" tabindex="-1" role="dialog" id="pushedit-modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-edit">Edit Sarpras</h5>
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
                                                        <option value="1">Mandiri</option>
                                                        <option value="2">Kampus</option>
                                                        <option value="3">Hibah</option>
                                                      </select>
                                                </div>
                                            </div>
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

                    <!-- Mulai modal lihat lampiran -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="show-lampiran" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">Lampiran Proposal</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table_lampiran" class="col-sm-12 table-responsive mb-3"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of modal lihat lampiran-->

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
        var table = $('#table_proposal').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('submission-of-proposal.index') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'laporan',name: 'laporan'},
                {data: 'nama_jenis_kegiatan',name: 'nama_jenis_kegiatan'},
                {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                {data: 'nama_user',name: 'nama_user',
                    render: function(data,type,row){
                        return row.nama_user_dosen || row.nama_user_mahasiswa
                    }
                },
                {data: 'action',name: 'action'},
                {data: 'status',name: 'status'},
            ]
        });
    });

    $(document).on('click','.lihat-proposal', function(){
        dataId = $(this).data('id');
        $.ajax({
            url: "{{route('check-status-proposal')}}",
            method: "GET",
            data: {proposal_id: dataId},
            success: function(response, data){
                $('#show-detail').modal('show');
                $("#table").html(response.card)
            }
        })
    });

    $('body').on('click','.alasan', function(){
        var data_ket = $(this).attr('data-alasan');
        $('#modal-judul-ket').html("Detil keterangan");
        $('#keterangan-modal').modal('show');
        $('#detil_ket').val(data_ket);
    });

    $('body').on('click','.edit-post', function(){
        var dataId = $(this).data('id');
        var dataTgl = $(this).attr('data-tgl');
        var dataItem = $(this).attr('data-item');
        var dataJumlah = $(this).attr('data-jumlah');
        var dataSumber = $(this).attr('data-sumber');
        $('#modal-judul-edit').html("Edit Sarpras");
        $('#pushedit-modal').modal('show');
        $('#e_sarpras_id').val(dataId);
        $('#e_tgl_kegiatan').val(dataTgl);
        $('#e_sarpras_item').val(dataItem);
        $('#e_jumlah').val(dataJumlah);
        $('#e_sumber').val(dataSumber);
    });
    if ($("#form-edit-sarpras").length > 0) {
        $("#form-edit-sarpras").validate({
            submitHandler: function (form) {
                var actionType = $('#btn-update').val();
                $('#btn-update').html('Updating..');

                $.ajax({
                    data: $('#form-edit-sarpras').serialize(),
                    url: "{{ route('update-pegajuan-sarpras') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-edit-sarpras').trigger("reset");
                        $('#pushedit-modal').modal('hide');
                        $('#btn-update').html('Update');
                        $('#table_proposal').DataTable().ajax.reload(null, true);
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
                        location.reload();
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
                        url: "submission-of-proposal/" + dataId,
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
                        $('#table_proposal').DataTable().ajax.reload(null, true);
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

    $('body').on('click','.v-lampiran', function(){
        var data_id = $(this).data('id');
        $.ajax({
            url: "{{route('view-lampiran-proposal')}}",
            method: "GET",
            data: {
                proposal_id: data_id,  
                "_token": "{{ csrf_token() }}",
            },
            success: function(response, data){
                $('#show-lampiran').modal('show');
                $("#table_lampiran").html(response.card)
            }
        })
    });

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
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Your data has been deleted.',
                            type: 'success',
                            timer: 2000
                        })
                        location.reload();
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