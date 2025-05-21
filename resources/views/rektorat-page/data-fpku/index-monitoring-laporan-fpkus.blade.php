@extends('layouts.backend')
@section('title','Laporan FPKU')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('rlaporanfpku')}}">@yield('title')</a>
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
                        <table class="table table-hover table-responsive" id="table_fpku">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Laporan</th>
                                <th>No FPKU</th>
                                <th>Nama Kegiatan</th>
                                <th>Ketua Pelaksana</th>
                                <th>Detail Anggaran</th>
                                <th>Tgl Kegiatan</th>
                                <th>Status</th>
                                <th>Lampiran</th>
                            </tr>
                            </thead>
                        </table>
                    </div>                    
                </div>
                <!-- Mulai modal lihat lampiran -->
                <div class="modal fade" tabindex="-1" role="dialog" id="show-lampiran" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title justify-content-center">Lampiran Laporan FPKU</h5>
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

                <!-- Mulai modal lihat detail anggaran -->
                <div class="modal fade mt-2" tabindex="-1" role="dialog" id="show-detail-anggaran" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title justify-content-center">Detail Anggaran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="table_detail_anggaran" class="col-sm-12 table-responsive"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of modal lihat detail anggaran-->

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
        var table = $('#table_fpku').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('index-monitoring-laporan-fpkus') }}",
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
                {data: 'undangan',name: 'undangan'},
                {data: 'no_surat_undangan',name: 'no_surat_undangan'},
                {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                {data: 'ketua_pelaksana',name: 'ketua_pelaksana'},
                {data: 'detail',name: 'detail'},
                {data: 'tgl_kegiatan',name: 'tgl_kegiatan'},
                {data: 'status',name: 'status'},
                {data: 'lampirans',name: 'lampirans'},
            ]
        });
    });

    $('body').on('click','.lihat-lampiran-laporan-fpku', function(){
        var data_id = $(this).data('id');
        $.ajax({
            url: "{{route('view-lampiran-laporan-fpku')}}",
            method: "GET",
            data: {
                fpku_id: data_id,  
                "_token": "{{ csrf_token() }}",
            },
            success: function(response, data){
                $('#show-lampiran').modal('show');
                $("#table_lampiran").html(response.card)
            }
        })
    });

    $('body').on('click','.lihat-detail', function(){
        var data_id = $(this).data('id');
        $.ajax({
            url: "{{route('lihat-detail-anggaran-fpku')}}",
            method: "GET",
            data: {
                laporan_fpku_id: data_id,  
                "_token": "{{ csrf_token() }}",
            },
            success: function(response, data){
                $('#show-detail-anggaran').modal('show');
                $("#table_detail_anggaran").html(response.card)
            }
        })
    });

</script>

@endsection