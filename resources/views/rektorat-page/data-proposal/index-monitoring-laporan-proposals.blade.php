@extends('layouts.backend')
@section('title','Data Proposals')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('index-monitoring-proposals')}}">@yield('title')</a>
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
                            <table class="table table-hover table-responsive" id="table_proposal">
                              <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Laporan</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Detail Anggaran</th>
                                    <th>Ketua Pelaksana</th>
                                    <th>Tgl Laporan dibuat</th>
                                    <th>Status</th>
                                </tr>
                              </thead>
                            </table>
                        </div>
                    </div>

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
        var table = $('#table_proposal').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('index-monitoring-laporan-proposals') }}",
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