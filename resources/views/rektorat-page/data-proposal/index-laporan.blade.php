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
        <a href="{{route('index-hal-laporan')}}">@yield('title')</a>
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

<div class="container-fluid flex-grow-1">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card table-responsive">
                    <div class="card-body">
                        <div class="col-sm-2 mb-3">
                            <select style="cursor:pointer;" class="select2 form-control" id="status" name="status" required>
                                <option value="all" selected>Semua Proposal (default)</option>
                                <option value="emp">Belum ada laporan</option>
                                <option value="pending">Pending</option>
                                <option value="accepted">Diterima</option>
                                <option value="denied">Ditolak</option>
                            </select>
                        </div>
                        <table class="table table-hover table-responsive" id="table_proposal">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Laporan</th>
                                <th>Nama Kegiatan</th>
                                <th>Detail Anggaran</th>
                                <th>Nama Pengaju</th>
                                <th>Tgl dibuat</th>
                                <th>Status (Validasi)</th>
                            </tr>
                            </thead>
                        </table>
                    </div>

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

                    <!-- Mulai modal lihat detail realisasi anggaran -->
                    <div class="modal fade mt-3" tabindex="-1" role="dialog" id="show-detail-anggaran" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">Detail Realisasi Anggaran</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table_detail_realisasi_anggaran" class="col-sm-12 table-responsive mb-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of modal lihat detail realisasi anggaran-->
                    
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
        fill_datatable();
        function fill_datatable(status = ''){
            var table = $('#table_proposal').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('index-hal-laporan') }}",
                    "type": "GET",
                    "data": function(data){
                        data.status = $('#status').val();
                    }
                },
                columns: [
                    {data: null,sortable:false,
                        render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, 
                    {data: 'laporan',name: 'laporan'},
                    {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                    {data: 'detail',name: 'detail'},
                    {data: 'nama_user',name: 'nama_user',
                    render: function(data,type,row){
                            return row.nama_pegawai || row.nama_user
                        }
                    },
                    {data: 'tgl_proposal',name: 'tgl_proposal',
                        render: function ( data, type, row ){
                            if(row.tgl_proposal == null){
                                return '&ndash;';
                            } else {
                                return moment(row.tgl_proposal).format("DD MMM YYYY")
                            }
                        }
                    },
                    {data: 'action',name: 'action'},
                ]
            });
        }
        $('#status').on('change', function(e){
            var status = this.value;

            if(status != ''){
                $('#table_proposal').DataTable().destroy();
                fill_datatable(status);
            } else {
                alert('Anda belum memilih filter.');
                $('#table_proposal').DataTable().destroy();
                fill_datatable();
            }
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

    $('body').on('click', '.tombol-yes', function () {
        var data_id = $(this).attr('data-id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Click yes to confirm that it's done!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, confirm!',
            showLoaderOnConfirm: true,
            preConfirm: function () {
                return $.ajax({
                    url: "{{ route('laporan-selesai') }}",
                    type: "POST",
                    data: {
                        proposal_id: data_id,
                        _token: '{{ csrf_token() }}',
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        $('.tombol-yes').prop('disabled', true); // Disable tombol selama proses
                    },
                    success: function (response) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message || 'Data saved successfully!',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary',
                            },
                            buttonsStyling: false,
                            timer: 2000,
                        });
                        $('#table_proposal').DataTable().ajax.reload(null, true);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Failed to save data!',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-primary',
                            },
                            buttonsStyling: false,
                            timer: 2000,
                        });
                    },
                    complete: function () {
                        $('.tombol-yes').prop('disabled', false); // Re-enable tombol setelah proses selesai
                    },
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
                    url: "{{route('r-report-approval-n')}}",
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

    $('body').on('click','.info-ditolak',function(){
    var dataKet = $(this).attr('data-keteranganditolak');
    alert(dataKet);
    });

    $('body').on('click','.lihat-detail', function(){
        var data_id = $(this).data('id');
        $.ajax({
            url: "{{route('lihat-detail-realisasi-anggaran')}}",
            method: "GET",
            data: {
                proposal_id: data_id,  
                "_token": "{{ csrf_token() }}",
            },
            success: function(response, data){
                $('#show-detail-anggaran').modal('show');
                $("#table_detail_realisasi_anggaran").html(response.card)
            }
        })
    });

</script>

@endsection