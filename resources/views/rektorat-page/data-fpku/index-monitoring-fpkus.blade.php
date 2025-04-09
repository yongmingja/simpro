@extends('layouts.backend')
@section('title','Monitor Data FPKU')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('index-monitoring-fpkus')}}">@yield('title')</a>
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
                                <th>Undangan</th>
                                <th>Nama Kegiatan</th>
                                <th>Tgl Kegiatan</th>
                                <th>Ketua Pelaksana</th>
                                <th>Peserta</th>
                                <th>Lampiran</th>
                                <th>History Delegasi</th>
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
                                <h5 class="modal-title justify-content-center">Lampiran FPKU</h5>
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
            ajax: "{{ route('index-monitoring-fpkus') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'undangan',name: 'undangan'},
                {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                {data: 'tgl_kegiatan',name: 'tgl_kegiatan',
                    render: function ( data, type, row ){
                        return moment(row.tgl_kegiatan).format("DD MMM YYYY")
                    }
                },
                {data: 'ketua_pelaksana',name: 'ketua_pelaksana'},
                {data: 'nama_pegawai',name: 'nama_pegawai'},
                {data: 'lampirans',name: 'lampirans'},
                {data: 'lihatDelegasi',name: 'lihatDelegasi'},
            ]
        });
    });

    $('body').on('click','.lihat-lampiran', function(){
        var data_id = $(this).data('id');
        $.ajax({
            url: "{{route('view-lampiran-data-fpku')}}",
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

    document.addEventListener('DOMContentLoaded', function() {
          if (document.getElementById('catatan_delegator')) {
              var editor = new Quill('#editor-delegasi', {
                  theme: 'snow'
              });
              var quillEditor = document.getElementById('catatan_delegator');
              editor.on('text-change', function() {
                  quillEditor.value = editor.root.innerHTML;
              });

              quillEditor.addEventListener('input', function() {
                  editor.root.innerHTML = quillEditor.value;
              });
          }
    });

    $('body').on('click','.lihat-delegasi', function(){
        var idFpku = $(this).data('id');
        $.ajax({
            url: "{{route('lihat-history-delegasi')}}",
            method: "GET",
            data: {fpku_id: idFpku},
            success: function(response, data){
                $('#show-history').modal('show');
                $("#table_show_history").html(response.card)
            }
        })
    });

</script>

@endsection