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
                                                    <option value="emp">Belum ada laporan</option>                                 
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
                                    <th>Laporan</th>
                                    <th>Kategori</th>
                                    <th>Kode Renstra</th>
                                    <th>Kode Pagu</th>
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
        fill_datatable();
        function fill_datatable(tahun_akademik = 'all', lembaga = 'all'){
            var table = $('#table_proposal').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('index-monitoring-laporan-proposals') }}",
                    "type": "GET",
                    "data": function(data){
                        data.tahun_akademik = $('#tahun_akademik').val();
                        data.lembaga = $('#lembaga').val();
                    }
                },
                columns: [
                    {data: null,sortable:false,
                        render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, 
                    {data: 'laporan',name: 'laporan'},
                    {data: 'nama_jenis_kegiatan',name: 'nama_jenis_kegiatan'},
                    {data: 'kode_renstra',name: 'kode_renstra'},
                    {data: 'kode_pagu',name: 'kode_pagu'},
                    {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                    {data: 'detail',name: 'detail'},
                    {data: 'nama_pegawai',name: 'nama_pegawai'},
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