@extends('layouts.backend')
@section('title','Undangan')

@section('breadcrumbs')
<div class="container-xxl">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('rundanganfpku')}}">@yield('title')</a>
      </li>
      <li class="breadcrumb-item active">Data</li>
    </ol>
</nav>
</div>
@endsection

@section('content')

<div class="container-xxl flex-grow-1">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card table-responsive">
                    <div class="card-body">
                        <table class="table table-hover table-responsive" id="table_fpku">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Validasi</th>
                                <th>Undangan</th>
                                <th>Nama Kegiatan</th>
                                <th>Tgl Kegiatan</th>
                                <th>Peserta</th>
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
        var table = $('#table_fpku').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('rundanganfpku') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'action',name: 'action'},
                {data: 'undangan',name: 'undangan'},
                {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                {data: 'tgl_kegiatan',name: 'tgl_kegiatan',
                    render: function ( data, type, row ){
                        return moment(row.tgl_kegiatan).format("DD MMM YYYY")
                    }
                },
                {data: 'nama_pegawai',name: 'nama_pegawai'},
            ]
        });
    });

    $('body').on('click','.tombol-yes', function(){
        var idFpku = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "Klik Ya, confirm untuk memvalidasi undangan",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, confirm!',
            showLoaderOnConfirm: true,
            preConfirm: function() {
                return new Promise(function(resolve) {
                    $.ajax({
                        url: "{{route('confirmundanganfpku')}}",
                        type: 'POST',
                        data: {id:idFpku},
                        dataType: 'json'
                    }).done(function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Your data has been validated.',
                            type: 'success',
                            timer: 2000
                        })
                        $('#table_fpku').DataTable().ajax.reload(null, true);
                    }).fail(function() {
                        Swal.fire({
                            title: 'Oops!',
                            text: 'Something went wrong with ajax!',
                            type: 'error',
                            timer: 2000
                        })
                    });
                });
            },
        });
    });

</script>

@endsection