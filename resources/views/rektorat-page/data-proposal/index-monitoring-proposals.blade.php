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
<style>
    td.details-control {
        cursor: pointer;
        text-align: center;
        font-size: 20px;
    }
    td.details-control .toggle-icon {
        color: #30e603; /* Blue for expand */
    }
    tr.shown td.details-control .toggle-icon.active {
        color: #da1e06; /* Red for collapse */
    }
    div.slider {
        display: none;
    }
</style>

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
                                        <div class="col-sm-2 mb-3">
                                            <fieldset class="form-group">
                                                <select style="cursor:pointer;" class="select2 form-control border" id="status" name="status" required>
                                                    <option value="all" selected>Semua Status (default)</option>
                                                    <option value="batal">Dibatalkan oleh user</option>
                                                    <option value="ditolakatasan">Ditolak Atasan</option>
                                                    <option value="diterimaatasan">Diterima Atasan</option>
                                                    <option value="ditolakrektorat">Ditolak Rektorat</option>
                                                    <option value="diterimarektorat">ACC Rektorat</option>
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-hover table-responsive" id="table_proposal">
                              <thead>
                                <tr>
                                    <th>Detail</th>
                                    <th></th>
                                    <th>Nama Kegiatan</th>
                                    <th>Kategori</th>
                                    <th>Kode Renstra</th>
                                    <th>Tgl Kegiatan</th>
                                    <th>Proposal Dibuat</th>
                                    <th>Unit Penyelenggara</th>
                                    <th>Status</th>
                                </tr>
                              </thead>
                            </table>
                        </div>
                    </div>

                    <!-- Mulai modal lampiran proposal -->
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
                    <!-- End of modal lampiran proposal -->

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
                            </div>
                        </div>
                    </div>
                    <!-- End of modal show history delegasi -->

                    <!-- Modal status sarpras -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="status-sarpras" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">Status Sarana Prasarana</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">                                
                                    <div id="table_sarpras" class="col-sm-12 table-responsive mb-3"></div>                                
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

    function formatData (item) {
        return '<div class="slider mb-2">'+   
        '<p class="mb-2">Detail lainnya:</p>'+
            '<button type="button" class="lihat-lampiran btn btn-outline-info btn-sm" data-id="'+item.id+'" title="Lihat data lampiran"><span class="bx bx-file"></span>  Lihat lampiran proposal</button>&nbsp;'+
            '<button type="button" class="detail-anggaran btn btn-outline-info btn-sm" data-id="'+item.id+'" title="Informasi detail anggaran"><span class="bx bx-money"></span>  Lihat detail anggaran</button>&nbsp;'+
            '<button type="button" class="detail-sarpras btn btn-outline-info btn-sm" data-id="'+item.id+'" title="Informasi detail sarpras"><span class="bx bx-car"></span> Lihat detail sarpras</button>&nbsp;'+
            '<button type="button" class="detail-history btn btn-outline-info btn-sm" data-id="'+item.id+'" title="Informasi histori delegasi"><span class="bx bx-history"></span> Lihat Delegasi</button>&nbsp;'+
            '</div>'
    }    

    // DATATABLE
    $(document).ready(function () {
        fill_datatable();
        function fill_datatable(lembaga = 'all', tahun_akademik = 'all', status = 'all'){
            var table = $('#table_proposal').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('index-monitoring-proposals') }}",
                    "type": "GET",
                    "data": function(data){
                        data.lembaga = $('#lembaga').val();
                        data.tahun_akademik = $('#tahun_akademik').val();
                        data.status = $('#status').val();
                    }
                },
                columns: [
                    {
                        data: null, 
                        class: "details-control", 
                        orderable: false,
                        render: function() {
                            return '<i class="bx bx-plus-circle toggle-icon"></i>';
                        }
                    },
                    {data: 'id',name: 'id', visible: false},
                    {data: 'preview',name: 'preview'},
                    {data: 'nama_jenis_kegiatan',name: 'nama_jenis_kegiatan'},
                    {data: 'kode_renstra',name: 'kode_renstra'},
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
                    {data: 'status',name: 'status'},                    
                ],
                createdRow: function(row, data, index) {
                    $('td', row).eq(1).attr("nowrap","nowrap");
                },
                
            });

            // Detail
            $('#table_proposal tbody').on('click', 'td.details-control', function(){
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                // Tutup semua baris lain sebelum membuka yang baru
                $('#table_proposal tbody tr.shown').not(tr).each(function() {
                    var otherRow = table.row($(this));
                    if (otherRow.child.isShown()) {
                        $('div.slider', otherRow.child()).slideUp(function(){
                            otherRow.child.hide();
                            $(this).removeClass('shown');
                        });
                        $(this).find('.toggle-icon').removeClass('bx-minus-circle active').addClass('bx-plus-circle');
                    }
                });

                // Cek apakah baris yang diklik sedang terbuka atau tertutup
                if (row.child.isShown()) {
                    $('div.slider', row.child()).slideUp(function(){
                        row.child.hide();
                        tr.removeClass('shown');
                    });
                    tr.find('.toggle-icon').removeClass('bx-minus-circle active').addClass('bx-plus-circle');
                } else {
                    row.child(formatData(row.data()), 'no-padding').show();
                    tr.addClass('shown');
                    $('div.slider', row.child()).slideDown();
                    tr.find('.toggle-icon').removeClass('bx-plus-circle').addClass('bx-minus-circle active');
                }
            });
        }
        $('#tahun_akademik, #lembaga, #status').on('change', function(e){
            var selectTahun = this.value;
            var selectLembaga = this.value;
            var selectStatus = this.value;

            if(selectTahun != '' || selectLembaga != '' || selectStatus != ''){
                $('#table_proposal').DataTable().destroy();
                fill_datatable(selectTahun, selectLembaga, selectStatus);
            } else {
                alert('Anda belum memilih filter.');
                $('#table_proposal').DataTable().destroy();
                fill_datatable();
            }
        });
    });

    // $('body').on('click','.v-lampiran', function(){
    //     var data_id = $(this).data('id');
    //     $.ajax({
    //         url: "{{route('view-lampiran-proposal')}}",
    //         method: "GET",
    //         data: {proposal_id: data_id},
    //         success: function(response, data){
    //             $('#show-detail').modal('show');
    //             $("#table_l").html(response.card)
    //         }
    //     })
    // });

    // $('body').on('click','.lihat-detail', function(){
    //     var data_id = $(this).data('id');
    //     $.ajax({
    //         url: "{{route('lihat-detail-anggaran')}}",
    //         method: "GET",
    //         data: {
    //             proposal_id: data_id,  
    //             "_token": "{{ csrf_token() }}",
    //         },
    //         success: function(response, data){
    //             $('#show-detail-anggaran').modal('show');
    //             $("#table_detail_anggaran").html(response.card)
    //         }
    //     })
    // });

    // $('body').on('click','.lihat-delegasi', function(){
    //     var id_proposal = $(this).data('id');
    //     $.ajax({
    //         url: "{{route('lihat-history-delegasi-proposal')}}",
    //         method: "GET",
    //         data: {proposal_id: id_proposal},
    //         success: function(response, data){
    //             $('#show-history').modal('show');
    //             $("#table_show_history").html(response.card)
    //         }
    //     })
    // });

    // $(document).on('click','.status-mon-sarpras', function(){
    //     dataId = $(this).data('id');
    //     $.ajax({
    //         url: "{{route('status-monitoring-sarpras')}}",
    //         method: "GET",
    //         data: {proposal_id: dataId},
    //         success: function(response, data){
    //             $('#status-sarpras').modal('show');
    //             $("#table_sarpras").html(response.card)
    //         }
    //     })
    // });

    ///// Tombol Details /////

    $(document).on('click', '.lihat-lampiran', function() {
        var proposalId = $(this).data('id');
        $.ajax({
            url: "{{route('view-lampiran-proposal')}}",
            method: "GET",
            data: {proposal_id: proposalId},
            success: function(response, data){
                $('#show-detail').modal('show');
                $("#table_l").html(response.card)
            }
        })
    });

    $(document).on('click', '.detail-anggaran', function() {
        var proposalId = $(this).data('id');
        $.ajax({
            url: "{{route('lihat-detail-anggaran')}}",
            method: "GET",
            data: {
                proposal_id: proposalId,  
                "_token": "{{ csrf_token() }}",
            },
            success: function(response, data){
                $('#show-detail-anggaran').modal('show');
                $("#table_detail_anggaran").html(response.card)
            }
        })
    });

    $(document).on('click', '.detail-sarpras', function() {
        var proposalId = $(this).data('id');
        $.ajax({
            url: "{{route('status-monitoring-sarpras')}}",
            method: "GET",
            data: {proposal_id: proposalId},
            success: function(response, data){
                $('#status-sarpras').modal('show');
                $("#table_sarpras").html(response.card)
            }
        })
    });

    $(document).on('click', '.detail-history', function() {
        var proposalId = $(this).data('id');
        $.ajax({
            url: "{{route('lihat-history-delegasi-proposal')}}",
            method: "GET",
            data: {proposal_id: proposalId},
            success: function(response, data){
                $('#show-history').modal('show');
                $("#table_show_history").html(response.card)
            }
        })
    });

    ///// Tombol Details /////

</script>

@endsection