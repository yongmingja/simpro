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
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row pl-4">
                                        <div class="col-sm-2 mb-3 ml-3">
                                            <fieldset class="form-group">
                                                <select style="cursor:pointer;" class="select2 form-control border" id="tahun_akademik" name="tahun_akademik" required>
                                                    <option value="all" selected>Semua TA (default)</option>                                    
                                                    @foreach($getYear as $y)
                                                        <option value="{{$y->id}}">{{$y->year}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-sm-2 mb-3">
                                            <fieldset class="form-group">
                                                <select style="cursor:pointer;" class="select2 form-control border" id="lembaga" name="lembaga" required>
                                                    <option value="all" selected>Semua Lembaga (default)</option>                                    
                                                    @foreach($getLembaga as $lembaga)
                                                        <option value="{{$lembaga->id}}">{{$lembaga->nama_fakultas_biro}}</option>
                                                    @endforeach
                                                    <option value="others">Lainnya (Rektorat)</option>
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-hover table-responsive" id="table_proposal">
                              <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Preview</th>
                                    <th>Kategori</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Tgl Kegiatan</th>
                                    <th>Proposal Dibuat</th>
                                    <th>Detail Anggaran</th>
                                    <th>Unit Penyelenggara</th>
                                    <th>Status</th>
                                    <th>History Delegasi</th>
                                </tr>
                              </thead>
                            </table>
                        </div>
                    </div>

                    <!-- Mulai modal lihat detail anggaran -->
                    <div class="modal fade mt-3" tabindex="-1" role="dialog" id="show-detail-anggaran" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">Detail Anggaran</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table_detail_anggaran" class="col-sm-12 table-responsive mb-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of modal lihat detail anggaran-->

                    <!-- Mulai modal show history delegasi  -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="show-history" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">History Delegasi Data ini</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table_show_history" class="col-sm-12 table-responsive mb-3"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of modal show history delegasi -->
                    
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
        function fill_datatable(lembaga = 'all', tahun_akademik = 'all'){
            var table = $('#table_proposal').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('index-monitoring-proposals') }}",
                    "type": "GET",
                    "data": function(data){
                        data.lembaga = $('#lembaga').val();
                        data.tahun_akademik = $('#tahun_akademik').val();
                    }
                },
                columns: [
                    {data: null,sortable:false,
                        render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, 
                    {data: 'preview',name: 'preview'},
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
                    {data: 'detail',name: 'detail'},
                    {data: 'nama_fakultas_biro',name: 'nama_fakultas_biro'},
                    {data: 'status',name: 'status'},
                    {data: 'history',name: 'history'},
                ]
            });
        }
        $('#tahun_akademik, #lembaga').on('change', function(e){
            var selectTahun = this.value;
            var selectLembaga = this.value;

            if(selectTahun != '' || selectLembaga != ''){
                $('#table_proposal').DataTable().destroy();
                fill_datatable(selectTahun, selectLembaga);
            } else {
                alert('Anda belum memilih filter.');
                $('#table_proposal').DataTable().destroy();
                fill_datatable();
            }
        });
    });

    $('body').on('click','.lihat-detail', function(){
        var data_id = $(this).data('id');
        $.ajax({
            url: "{{route('lihat-detail-anggaran')}}",
            method: "GET",
            data: {
                proposal_id: data_id,  
                "_token": "{{ csrf_token() }}",
            },
            success: function(response, data){
                $('#show-detail-anggaran').modal('show');
                $("#table_detail_anggaran").html(response.card)
            }
        })
    });

    $('body').on('click','.lihat-delegasi', function(){
        var id_proposal = $(this).data('id');
        $.ajax({
            url: "{{route('lihat-history-delegasi-proposal')}}",
            method: "GET",
            data: {proposal_id: id_proposal},
            success: function(response, data){
                $('#show-history').modal('show');
                $("#table_show_history").html(response.card)
            }
        })
    });

</script>

@endsection