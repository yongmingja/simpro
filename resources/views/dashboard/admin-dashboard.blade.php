@extends('layouts.backend')
@section('title','Dashboard')

@section('content')
    <!-- Content -->
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="row">
            <h5>Hallo, {{ Auth::user()->nama_pegawai }}</h5>
            <h4>Selamat datang di Dashboard Sistem Pengajuan Proposal Kegiatan</h4>
        </div>
        @if($recentRole == 'PEGS')
        <div class="row">
          <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-primary h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                  <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-file bx-tada"></i></span>
                  </div>
                  <h4 class="ms-1 mb-0">{{$countProposals}}</h4>
                </div>
                <p class="mb-1">Total Proposal</p>
                <p class="mb-0">
                  <span class="fw-medium me-1">{{$countProposals}}</span>
                  <small class="text-muted">Pengajuan Proposal</small>
                </p>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-warning h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                  <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-warning"><i class='bx bx-loader-circle bx-spin fs-4'></i></span>
                  </div>
                  <h4 class="ms-1 mb-0">{{$countProposalOnGoing}}</h4>
                </div>
                <p class="mb-1">Proposal Pending</p>
                <p class="mb-0">
                  <span class="fw-medium me-1">{{$countProposalOnGoing}}</span>
                  <small class="text-muted">Proposal Pending</small>
                </p>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-danger h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                  <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-danger"><i class='bx bx-error bx-tada'></i></span>
                  </div>
                  <h4 class="ms-1 mb-0">{{$countProposalDeclined}}</h4>
                </div>
                <p class="mb-1">Proposal Ditolak</p>
                <p class="mb-0">
                  <span class="fw-medium me-1">{{$countProposalDeclined}}</span>
                  <small class="text-muted">Proposal Ditolak</small>
                </p>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-info h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                  <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-success"><i class='bx bx-check-double bx-tada'></i></span>
                  </div>
                  <h4 class="ms-1 mb-0">{{$countProposalAcc}}</h4>
                </div>
                <p class="mb-1">Proposal Diterima</p>
                <p class="mb-0">
                  <span class="fw-medium me-1">{{$countProposalAcc}}</span>
                  <small class="text-muted">Proposal Diterima</small>
                </p>
              </div>
            </div>
          </div>
        </div> 
        @endif

        @if($recentRole == 'SADM' || $recentRole == 'ADU')
        <div class="row">
          <div class="col-md-6 col-lg-6 col-xl-6 mb-4 mb-xl-0">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-2">Total Proposal</h5>
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
                <h5 class="card-title mb-2">Total Laporan Proposal</h5>
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

        @if($recentRole == 'WAREK')
          <div class="row">
            <div class="col-md-4 col-lg-4 col-xl-4 mb-4 mb-xl-0">
              <div class="card h-100">
                <div class="card-header">
                  <h5 class="card-title mb-2">Total Proposal as Validator</h5>
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
            <div class="col-md-4 col-lg-4 col-xl-4 mb-4 mb-xl-0">
              <div class="card h-100">
                <div class="card-header">
                  <h5 class="card-title mb-2">Total Laporan Proposal as Validator</h5>
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
            <!-- Colom ke 3 -->
            <div class="col-md-4 col-lg-4 col-xl-4 mb-4 mb-xl-0">
              <div class="card h-100">
                <div class="card-header">
                  <h5 class="card-title mb-2">Total Proposal Saya</h5>
                  <h1 class="display-6 fw-normal mb-0">{{$countProposals}}</h1>
                </div>
                <div class="card-body">
                  <ul class="p-0 m-0">
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-warning me-2"></span> Jumlah Proposal Pending
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$countProposalOnGoing}}</span>
                      </div>
                    </li>
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-success me-2"></span> Jumlah Proposal Diterima
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$countProposalAcc}}</span>
                      </div>
                    </li>                    
                    <li class="mb-3 d-flex justify-content-between">
                      <div class="d-flex align-items-center lh-1 me-3">
                        <span class="badge badge-dot bg-danger me-2"></span> Jumlah Proposal Ditolak
                      </div>
                      <div class="d-flex gap-3">
                        <span class="fw-medium">{{$countProposalDeclined}}</span>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <div class="divider text-start">
            <div class="divider-text">Grafik Realisasi Anggaran Proposal Kegiatan*</div>
          </div>
          <div class="row mt-3">
            <div class="col-sm-12">
              <div id="chart"></div>
              <div><p style="font-size: 10px;">*Kategori Sumber Dana: Kampus</p></div>
            </div>
          </div>
          
        @endif

        @if($recentRole == 'PEG' || $recentRole == 'RKT')
          <div class="row">
            <div class="col-md-6 col-lg-6 col-xl-6 mb-4 mb-xl-0">
              <div class="card h-100">
                <div class="card-header">
                  <h5 class="card-title mb-2">Total Proposal</h5>
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
                  <h5 class="card-title mb-2">Total Laporan Proposal</h5>
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

    function formatRupiah(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(value);
    }


    // Chart
    var options = {
      series: [
          {
              name: 'Realisasi Anggaran',
              data: {!! json_encode($mergedData) !!}
          }
      ],
      xaxis: {
        categories: {!! json_encode($mergedData->pluck('x')) !!}, // Nama kode fakultas/biro
        labels: {
            style: {
                colors: '#0a8fd1', // Warna untuk setiap label
                fontSize: '12px', // Ukuran font
                fontWeight: 'bold' // Ketebalan font
            }
        }
      },
      yaxis: {
        categories: {!! json_encode($mergedData->pluck('y')) !!},
        labels: {
            style: {
                colors: '#0a8fd1', // Warna untuk label y-axis
                fontSize: '12px', // Ukuran font untuk label y-axis
                fontWeight: 'bold' // Ketebalan font label y-axis
            },
            formatter: function(value) {
              return formatRupiah(value);
            }
        }
      },
      chart: {
          height: 350,
          type: 'bar'
      },
      plotOptions: {
          bar: {
              columnWidth: '60%'
          }
      },
      tooltip: {
          y: {
              formatter: function(value) {
                  return formatRupiah(value);
              }
          }
      },
      colors: ['#007BFF'],
      dataLabels: {
          enabled: true,
          formatter: function(value) {
              return formatRupiah(value);
          }
      },
      legend: {
          show: true,
          showForSingleSeries: true,
          customLegendItems: ['Realisasi Anggaran', 'Total RKAT'],
          markers: {
              fillColors: ['#007BFF', '#ed2da7']
          },
          labels: {
            colors: '#0a8fd1',
          }
      }
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();

</script>
@endsection