@extends('layouts.backend')
@section('title','Dashboard')

@section('content')
    <!-- Content -->
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="row">

            <div class="col-12">
                <div class="card table-responsive">
                    <div class="card-body">
                            <table class="table table-hover table-responsive" id="table_proposal">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Proposal</th>
                                  <th>Nama Kegiatan</th>
                                  <th>Tgl Kegiatan</th>
                                  <th>Proposal Dibuat</th>
                                  <th>Nama Fakultas</th>
                                  <th>Nama Prodi</th>
                                  <th>Lihat</th>
                                  <th width="12%;">Status</th>
                                  <th>Lampiran</th>
                                </tr>
                              </thead>
                            </table>
                        </div>
                    </div>

                    <!-- Modal validasi proposal -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="validasi-prop" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">Validasi Rencana Anggaran</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table" class="col-sm-12 table-responsive mb-3"></div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of validasi proposal-->

                    <!-- Modal validasi proposal -->
                    <div class="modal fade" id="add-keterangan-modal" aria-hidden="true">
                        <div class="modal-dialog ">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-add-keterangan" name="form-add-keterangan" class="form-horizontal">
                                        <div class="row">
                                            <div class="mb-3">
                                                <input type="hidden" class="form-control" id="propsl_id" name="propsl_id">
                                                <label for="keterangan_ditolak" class="form-label">Keterangan ditolak</label>
                                                <textarea class="form-control" id="keterangan_ditolak" name="keterangan_ditolak" rows="5"></textarea>
                                                <span class="text-danger" id="alasanErrorMsg" style="font-size: 10px;"></span>
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
                    <!-- End of validasi proposal-->

                    <!-- Mulai modal detail -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="show-detail" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">Lampiran Proposal</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table_l" class="col-sm-12 table-responsive mb-3"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of modal detail-->

                </div>
            </div>
        </div>
    
    </div>
    <!-- / Content -->

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
            ajax: "{{ route('dashboard-rektorat') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'nama_jenis_kegiatan',name: 'nama_jenis_kegiatan'},
                {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                {data: 'tgl_event',name: 'tgl_event',
                    render: function ( data, type, row ){
                        return moment(row.tgl_event).format("DD MMM YYYY")
                    }
                },
                {data: 'created_at',name: 'created_at',
                    render: function ( data, type, row ){
                        return moment(row.created_at).format("DD MMM YYYY")
                    }
                },
                {data: 'nama_fakultas_biro',name: 'nama_fakultas_biro'},
                {data: 'nama_prodi_biro',name: 'nama_prodi_biro'},
                {data: 'action',name: 'action'},
                {data: 'validasi',name: 'validasi'},
                {data: 'vlampiran',name: 'vlampiran'},
            ]
        });
    });

    $('body').on('click','.tombol-yes', function(){
        var data_id = $(this).attr('data-id');            
        Swal.fire({
            title: 'Are you sure?',
            text: "Please click yes to accept the proposal!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, accept it!',
            showLoaderOnConfirm: true,
            preConfirm: function() {
                return new Promise(function(resolve) {
                    $.ajax({
                    url: "{{route('approval-y')}}",
                    type: "POST",
                    data: {
                        proposal_id: data_id,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (data) {
                        Swal.fire({
                            title: 'Agree!',
                            text: 'Data saved successfully!',
                            type: 'success',
                            customClass: {
                            confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false,
                            timer: 2000
                        })
                        $('#table_proposal').DataTable().ajax.reload(null, true);
                    }
                });
                });
            },
        });
    });

    $('body').on('click','.tombol-no', function(){
        var data_id = $(this).attr('data-id');
        $('#form-add-keterangan').trigger("reset");
        $('#modal-judul').html("Tambah keterangan");
        $('#add-keterangan-modal').modal('show');
        $('#propsl_id').val(data_id);
    });
    if ($("#form-add-keterangan").length > 0) {
        $("#form-add-keterangan").validate({
            submitHandler: function (form) {
                var actionType = $('#tombol-simpan').val();
                $('#tombol-simpan').html('Saving..');

                $.ajax({
                    data: $('#form-add-keterangan').serialize(), 
                    url: "{{route('approval-n')}}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-add-keterangan').trigger("reset");
                        $('#add-keterangan-modal').modal('hide');
                        $('#tombol-simpan').html('Save');
                        $('#table_proposal').DataTable().ajax.reload(null, true);
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

    $('body').on('click','.v-lampiran', function(){
        var data_id = $(this).data('id');
        $.ajax({
            url: "{{route('view-lampiran-proposal')}}",
            method: "GET",
            data: {proposal_id: data_id},
            success: function(response, data){
                $('#show-detail').modal('show');
                $("#table_l").html(response.card)
            }
        })
    })
</script>
@endsection