@extends('layouts.backend')
@section('title','Laporan Proposal')

@section('breadcrumbs')
<div class="container-xxl">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="javascript:void(0)">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('index-hal-laporan')}}">@yield('title')</a>
      </li>
      <li class="breadcrumb-item active">Data</li>
    </ol>
</nav>
</div>
@endsection
<style>
    .horizontal-timeline .items {
    border-top: 3px solid #e9ecef;
    }

    .horizontal-timeline .items .items-list {
    display: block;
    position: relative;
    text-align: center;
    padding-top: 70px;
    margin-right: 0;
    }

    .horizontal-timeline .items .items-list:before {
    content: "";
    position: absolute;
    height: 36px;
    border-right: 2px dashed #dee2e6;
    top: 0;
    }

    .horizontal-timeline .items .items-list .event-date {
    position: absolute;
    top: 36px;
    left: 0;
    right: 0;
    width: 90px;
    margin: 0 auto;
    font-size: 0.7rem;
    padding-top: 8px;
    }

    @media (min-width: 1140px) {
    .horizontal-timeline .items .items-list {
        display: inline-block;
        width: 24%;
        padding-top: 45px;
    }

    .horizontal-timeline .items .items-list .event-date {
        top: -40px;
    }
    }
</style>

@section('content')

<div class="container-xxl flex-grow-1">
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
                                <th>Proposal</th>
                                <th>Nama Kegiatan</th>
                                <th>Nama Pengaju</th>
                                <th>Tgl dibuat</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    
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
            ajax: "{{ route('index-hal-laporan') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'laporan',name: 'laporan'},
                {data: 'nama_jenis_kegiatan',name: 'nama_jenis_kegiatan'},
                {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                {data: 'nama_user',name: 'nama_user',
                render: function(data,type,row){
                        return row.nama_pegawai || row.nama_user
                    }
                },
                {data: 'tgl_proposal',name: 'tgl_proposal',render: function ( data, type, row ) {return moment(row.tgl_proposal).format("LL")}},
                {data: 'action',name: 'action'},
            ]
        });
    });

    $(document).on('click','.lihat-proposal', function(){
        dataId = $(this).data('id');
        $.ajax({
            url: "{{route('check-status-proposal')}}",
            method: "GET",
            data: {proposal_id: dataId},
            success: function(response, data){
                $('#show-detail').modal('show');
                $("#table").html(response.card)
            }
        })
    });

    $('body').on('click','.tombol-yes', function(){
        var data_id = $(this).attr('data-id');
        $.ajax({
            url: "{{route('laporan-selesai')}}",
            type: "POST",
            data: {
                proposal_id: data_id,
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
                $('#table_proposal').DataTable().ajax.reload(null, true);
            }
        });
    });
</script>

@endsection