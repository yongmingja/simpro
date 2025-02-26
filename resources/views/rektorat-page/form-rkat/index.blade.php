@extends('layouts.backend')
@section('title','Form RKAT')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('index-form-rkat')}}">@yield('title')</a>
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
                        <!-- MULAI TOMBOL TAMBAH -->
                        <div class="mb-3">
                            <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body" id="tombol-tambah"><button type="button" class="btn btn-primary"><i class="bx bx-plus-circle bx-spin-hover"></i> New data</button></a>
                        </div>
                        
                        <!-- AKHIR TOMBOL -->
                            <table class="table table-hover table-responsive" id="table_rkat">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Sasaran Strategi</th>
                                  <th>Program Strategis</th>
                                  <th>Kode Renstra</th>
                                  <th>Nama Kegiatan</th>
                                  <th>Kode Pagu</th>
                                  <th>Total</th>
                                  <th>Actions</th>
                                </tr>
                              </thead>
                            </table>
                        </div>
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
        var table = $('#table_rkat').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('index-form-rkat') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'sasaran_strategi',name: 'sasaran_strategi'},
                {data: 'program_strategis',name: 'program_strategis'},
                {data: 'kode_renstra',name: 'kode_renstra'},
                {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                {data: 'kode_pagu',name: 'kode_pagu'},
                {data: 'total',  render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' )},
                {data: 'action',name: 'action'},
            ]
        });
    });

    $('body').on('click','.tombol-yes', function(){
        var data_id = $(this).attr('data-id');
        Swal.fire({
            title: 'Are you sure?',
            text: "Please click yes to accept the data!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, accept it!',
            showLoaderOnConfirm: true,
            preConfirm: function() {
                return new Promise(function(resolve) {
                    $.ajax({
                        url: "{{route('rkat-approval-y')}}",
                        type: "POST",
                        data: {
                            rkat_id: data_id,
                            _token: '{{csrf_token()}}'
                        },
                        dataType: 'json',
                        success: function (data) {
                            $('#table_rkat').DataTable().ajax.reload(null, true);
                            Swal.fire({
                                title: 'Agree!',
                                text: 'Data accepted successfully!',
                                type: 'success',
                                customClass: {
                                confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false,
                                timer: 2000
                            })
                        }
                    });
                });
            },
        });
        
    });

    $('body').on('click','.tombol-no', function(){
        var data_id = $(this).attr('data-id');
        Swal.fire({
            title: 'Are you sure?',
            text: "Please click yes to ignore the data!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, ignore it!',
            showLoaderOnConfirm: true,
            preConfirm: function() {
                return new Promise(function(resolve) {
                    $.ajax({
                        url: "{{route('rkat-approval-n')}}",
                        type: "POST",
                        data: {
                            rkat_id: data_id,
                            _token: '{{csrf_token()}}'
                        },
                        dataType: 'json',
                        success: function (data) {
                            $('#table_rkat').DataTable().ajax.reload(null, true);
                            Swal.fire({
                                title: 'Agree!',
                                text: 'Data ignored successfully!',
                                type: 'success',
                                customClass: {
                                confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false,
                                timer: 2000
                            })
                        }
                    });
                });
            },
        });
        
    });

</script>

@endsection