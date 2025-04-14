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
                                    <th>Nama Kegiatan</th>
                                    <th>Tgl Kegiatan</th>
                                    <th>Proposal Dibuat</th>
                                    <th>Sarpras</th>
                                    <th>Unit Penyelenggara</th>
                                </tr>
                              </thead>
                            </table>
                        </div>
                    </div>

                    <!-- Modal status sarpras -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="status-sarpras" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">Status Sarana Prasarana</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table" class="col-sm-12 table-responsive mb-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of status sarpras-->
                    
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
                    url: "{{ route('index-monitoring-sarpras') }}",
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
                    {data: 'action',name: 'action'},
                    {data: 'nama_fakultas_biro',name: 'nama_fakultas_biro',
                        render: function(data, type, row) {
                            if(row.nama_fakultas_biro != null || row.nama_prodi_biro != null) {                                
                                if(row.nama_fakultas_biro === row.nama_prodi_biro){
                                    return row.nama_fakultas_biro
                                } else {
                                    return row.nama_fakultas_biro +' &bull; '+ row.nama_prodi_biro
                                }
                            } else {
                                return '';
                            }
                        }
                    },                    
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

    $(document).on('click','.status-mon-sarpras', function(){
        dataId = $(this).data('id');
        $.ajax({
            url: "{{route('status-monitoring-sarpras')}}",
            method: "GET",
            data: {proposal_id: dataId},
            success: function(response, data){
                $('#status-sarpras').modal('show');
                $("#table").html(response.card)
            }
        })
    });

</script>

@endsection