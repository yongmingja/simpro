@extends('layouts.backend')
@section('title','Dashboard')

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Card Border Shadow -->
        <div class="row">
            <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-primary"><i class="bx bxs-group"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0">@php $data = \App\Setting\Mahasiswa::all(); echo $data->count(); @endphp</h4>
                </div>
                <p class="mb-1">Mahasiswa</p>
                <p class="mb-0">
                    <span class="fw-medium me-1">Total mahasiswa</span>
                </p>
                </div>
            </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-warning"><i class='bx bx-group'></i></span>
                    </div>
                    <h4 class="ms-1 mb-0">@php $data = \App\Setting\Dosen::all(); echo $data->count(); @endphp</h4>
                </div>
                <p class="mb-1">Dosen</p>
                <p class="mb-0">
                    <span class="fw-medium me-1">Total dosen</span>
                </p>
                </div>
            </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-danger"><i class='bx bx-group'></i></span>
                    </div>
                    <h4 class="ms-1 mb-0">@php $data = \App\Setting\Dekan::all(); echo $data->count(); @endphp</h4>
                </div>
                <p class="mb-1">Dekan</p>
                <p class="mb-0">
                    <span class="fw-medium me-1">Total dekan</span>
                </p>
                </div>
            </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-info"><i class='bx bx-group'></i></span>
                    </div>
                    <h4 class="ms-1 mb-0">@php $data = \App\Setting\Rektorat::all(); echo $data->count(); @endphp</h4>
                </div>
                <p class="mb-1">Rektorat</p>
                <p class="mb-0">
                    <span class="fw-medium me-1">User Rektorat</span>
                </p>
                </div>
            </div>
            </div>
        </div>
        <!--/ Card Border Shadow -->

        <div class="row">
            <h5>Hallo, @if (Str::length(Auth::guard('pegawai')->user()) > 0 )
                {{ Auth::guard('pegawai')->user()->nama_pegawai }}
                @elseif(Str::length(Auth::guard('mahasiswa')->user()) > 0)
                {{ Auth::guard('mahasiswa')->user()->name }}
                @endif</h5>
            <h4>Selamat datang di Dashboard Sistem Pengajuan Proposal</h4>
        </div>

        <!-- On route vehicles Table -->
        <!-- <div class="col-12 order-5">
            <div class="card">
                <div class="card-body">
                    <table class="table table-hover table-responsive" id="table_sarpras">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Tgl Kegiatan</th>
                          <th>Sarpras Item</th>
                          <th>Jumlah</th>
                          <th>Actions</th>
                          <th>Ket</th>
                        </tr>
                      </thead>
                    </table>
                </div>

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
                                            <input type="hidden" class="form-control" id="sarpras_id" name="sarpras_id">
                                            <label for="alasan" class="form-label">Keterangan ditolak</label>
                                            <textarea class="form-control" id="alasan" name="alasan" rows="5"></textarea>
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

                {{-- Info detil keterangan --}}
                <div class="modal fade" id="keterangan-modal" aria-hidden="true">
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
            </div>
            </div>
        </div> -->
        <!--/ On route vehicles Table -->
        
    
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
        var table = $('#table_sarpras').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('data-dash-admin') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'tgl_kegiatan',name: 'tgl_kegiatan'},
                {data: 'sarpras_item',name: 'sarpras_item'},
                {data: 'jumlah',name: 'jumlah'},
                {data: 'action',name: 'action'},
                {data: 'detil',name: 'detil'},
            ]
        });
    });

    $('body').on('click','.alasan', function(){
        var data_ket = $(this).attr('data-ket');
        $('#modal-judul-ket').html("Detil keterangan");
        $('#keterangan-modal').modal('show');
        $('#detil_ket').val(data_ket);
    });

    $('body').on('click','.tombol-yes', function(){
        var data_id = $(this).attr('data-id');
        $.ajax({
            url: "{{route('valid-y')}}",
            type: "POST",
            data: {
                sarpras_id: data_id,
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
                location.reload();
            }
        });
    });

    $('body').on('click','.tombol-no', function(){
        var data_id = $(this).data('id');
        $('#form-add-keterangan').trigger("reset");
        $('#modal-judul').html("Tambah keterangan");
        $('#add-keterangan-modal').modal('show');
        $('#sarpras_id').val(data_id);
    });

    if ($("#form-add-keterangan").length > 0) {
        $("#form-add-keterangan").validate({
            submitHandler: function (form) {
                var actionType = $('#tombol-simpan').val();
                $('#tombol-simpan').html('Saving..');

                $.ajax({
                    data: $('#form-add-keterangan').serialize(), 
                    url: "{{route('valid-n')}}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-add-keterangan').trigger("reset");
                        $('#add-keterangan-modal').modal('hide');
                        $('#tombol-simpan').html('Save');
                        $('#table_sarpras').DataTable().ajax.reload(null, true);
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
</script>
@endsection