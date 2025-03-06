@extends('layouts.backend')
@section('title','Dashboard')

@section('content')
    <!-- Content -->
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="row">
            <h5>Hallo, @if (Str::length(Auth::guard('pegawai')->user()) > 0 )
                {{ Auth::guard('pegawai')->user()->nama_pegawai }}
                @elseif(Str::length(Auth::guard('mahasiswa')->user()) > 0)
                {{ Auth::guard('mahasiswa')->user()->name }}
                @endif</h5>
            <h4>Selamat datang di Dashboard Sistem Pengajuan Proposal Kegiatan</h4>
        </div>
        @if($recentRole == 'DSN' || $recentRole == 'BRO')
        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-3 col-md-3 col-lg-3 mb-4">
              <div class="card h-100">
                <div class="card-body text-center">
                  <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-file fs-4"></i></span>
                  </div>
                  <span class="d-block text-nowrap">Total Proposals</span>
                  <h2 class="mb-0">{{$countProposals}}</h2>
                </div>
              </div>
            </div>
            <div class="col-3 col-md-3 col-lg-3 mb-4">
              <div class="card h-100">
                <div class="card-body text-center">
                  <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-check-double fs-4"></i></span>
                  </div>
                  <span class="d-block text-nowrap">Proposals Accepted</span>
                  <h2 class="mb-0">{{$countProposalAcc}}</h2>
                </div>
              </div>
            </div>
            <div class="col-3 col-md-3 col-lg-3 mb-4">
              <div class="card h-100">
                <div class="card-body text-center">
                  <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-warning"><i class="bx bx-loader-circle bx-spin fs-4"></i></span>
                  </div>
                  <span class="d-block text-nowrap">Proposal Pending (Declined or Unarchived)*</span>
                  <h2 class="mb-0">{{$countProposalOnGoing}}</h2>
                </div>
              </div>
            </div>
            <div class="col-3 col-md-3 col-lg-3 mb-4">
              <div class="card h-100">
                <div class="card-body text-center">
                  <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-danger"><i class="bx bx-x fs-4"></i></span>
                  </div>
                  <span class="d-block text-nowrap">Proposals Declined or Archived</span>
                  <h2 class="mb-0">{{$countProposalDeclined}}</h2>
                </div>
              </div>
            </div>
        </div>  
        @endif

        @if($recentRole == 'SADM')
        <div class="row">
          <div class="col-md-6 col-lg-6 col-xl-6 mb-4 mb-xl-0">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-2">Total Proposals</h5>
                <h1 class="display-6 fw-normal mb-0">{{$totalProposalsSadm}}</h1>
              </div>
              <div class="card-body">
                <ul class="p-0 m-0">
                  <li class="mb-3 d-flex justify-content-between">
                    <div class="d-flex align-items-center lh-1 me-3">
                      <span class="badge badge-dot bg-warning me-2"></span> Jumlah Proposal Pending
                    </div>
                    <div class="d-flex gap-3">
                      <span class="fw-medium">{{$totalProposalPendingSadm}}</span>
                    </div>
                  </li>
                  <li class="mb-3 d-flex justify-content-between">
                    <div class="d-flex align-items-center lh-1 me-3">
                      <span class="badge badge-dot bg-success me-2"></span> Jumlah Proposal Diterima
                    </div>
                    <div class="d-flex gap-3">
                      <span class="fw-medium">{{$totalProposalDiterimaSadm}}</span>
                    </div>
                  </li>                    
                  <li class="mb-3 d-flex justify-content-between">
                    <div class="d-flex align-items-center lh-1 me-3">
                      <span class="badge badge-dot bg-danger me-2"></span> Jumlah Proposal Ditolak
                    </div>
                    <div class="d-flex gap-3">
                      <span class="fw-medium">{{$totalProposalDitolakSadm}}</span>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <!-- Colom ke 2 -->
          <div class="col-md-6 col-lg-6 col-xl-6 mb-4 mb-xl-0">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-2">Total Laporan Proposals</h5>
                <h1 class="display-6 fw-normal mb-0">{{$totalLaporanProposalsSadm}}</h1>
              </div>
              <div class="card-body">
                <ul class="p-0 m-0">
                  <li class="mb-3 d-flex justify-content-between">
                    <div class="d-flex align-items-center lh-1 me-3">
                      <span class="badge badge-dot bg-warning me-2"></span> Jumlah Proposal Pending
                    </div>
                    <div class="d-flex gap-3">
                      <span class="fw-medium">{{$totalLaporanProposalPendingSadm}}</span>
                    </div>
                  </li>
                  <li class="mb-3 d-flex justify-content-between">
                    <div class="d-flex align-items-center lh-1 me-3">
                      <span class="badge badge-dot bg-success me-2"></span> Jumlah Proposal Diterima
                    </div>
                    <div class="d-flex gap-3">
                      <span class="fw-medium">{{$totalLaporanProposalDiterimaSadm}}</span>
                    </div>
                  </li>                    
                  <li class="mb-3 d-flex justify-content-between">
                    <div class="d-flex align-items-center lh-1 me-3">
                      <span class="badge badge-dot bg-danger me-2"></span> Jumlah Proposal Ditolak
                    </div>
                    <div class="d-flex gap-3">
                      <span class="fw-medium">{{$totalLaporanProposalDitolakSadm}}</span>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        @endif

        @if($recentRole == 'WRSDP' || $recentRole == 'WRAK')
          <div class="row">
            <div class="col-md-6 col-lg-6 col-xl-6 mb-4 mb-xl-0">
              <div class="card h-100">
                <div class="card-header">
                  <h5 class="card-title mb-2">Total Proposals</h5>
                  <h1 class="display-6 fw-normal mb-0">{{$totalProposals}}</h1>
                </div>
                <div class="card-body">
                  <ul class="p-0 m-0">
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-warning me-2"></span> Jumlah Proposal Pending
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$totalProposalPending}}</span>
                      </div>
                    </li>
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-success me-2"></span> Jumlah Proposal Diterima
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$totalProposalDiterima}}</span>
                      </div>
                    </li>                    
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-danger me-2"></span> Jumlah Proposal Ditolak
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$totalProposalDitolak}}</span>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <!-- Colom ke 2 -->
            <div class="col-md-6 col-lg-6 col-xl-6 mb-4 mb-xl-0">
              <div class="card h-100">
                <div class="card-header">
                  <h5 class="card-title mb-2">Total Laporan Proposals</h5>
                  <h1 class="display-6 fw-normal mb-0">{{$totalLaporanProposals}}</h1>
                </div>
                <div class="card-body">
                  <ul class="p-0 m-0">
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-warning me-2"></span> Jumlah Proposal Pending
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$totalLaporanProposalPending}}</span>
                      </div>
                    </li>
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-success me-2"></span> Jumlah Proposal Diterima
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$totalLaporanProposalDiterima}}</span>
                      </div>
                    </li>                    
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-danger me-2"></span> Jumlah Proposal Ditolak
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$totalLaporanProposalDitolak}}</span>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        @endif

        @if($recentRole == 'DKN' || $recentRole == 'BRO')
          <div class="row">
            <div class="col-md-6 col-lg-6 col-xl-6 mb-4 mb-xl-0">
              <div class="card h-100">
                <div class="card-header">
                  <h5 class="card-title mb-2">Total Proposals</h5>
                  <h1 class="display-6 fw-normal mb-0">{{$totalProposalsDekanBiro}}</h1>
                </div>
                <div class="card-body">
                  <ul class="p-0 m-0">
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-warning me-2"></span> Jumlah Proposal Pending
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$totalProposalPendingDekanBiro}}</span>
                      </div>
                    </li>
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-success me-2"></span> Jumlah Proposal Diterima
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$totalProposalDiterimaDekanBiro}}</span>
                      </div>
                    </li>                    
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-danger me-2"></span> Jumlah Proposal Ditolak
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$totalProposalDitolakDekanBiro}}</span>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <!-- Colom ke 2 -->
            <div class="col-md-6 col-lg-6 col-xl-6 mb-4 mb-xl-0">
              <div class="card h-100">
                <div class="card-header">
                  <h5 class="card-title mb-2">Total Laporan Proposals</h5>
                  <h1 class="display-6 fw-normal mb-0">{{$totalLaporanProposalsDekanBiro}}</h1>
                </div>
                <div class="card-body">
                  <ul class="p-0 m-0">
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-warning me-2"></span> Jumlah Proposal Pending
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$totalLaporanProposalPendingDekanBiro}}</span>
                      </div>
                    </li>
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-success me-2"></span> Jumlah Proposal Diterima
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$totalLaporanProposalDiterimaDekanBiro}}</span>
                      </div>
                    </li>                    
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-danger me-2"></span> Jumlah Proposal Ditolak
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$totalLaporanProposalDitolakDekanBiro}}</span>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        @endif
    
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