@extends('layouts.backend')
@section('title','Dashboard')

@section('content')
    <!-- Content -->
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bxs-group"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">@php $data = \App\Setting\Mahasiswa::all(); echo $data->count(); @endphp</h4>
                    </div>
                    <p class="mb-1">Mahasiswa</p>
                    <p class="mb-0">
                        <span class="fw-medium me-1">Total mahasiswa</span>
                    </p>
                    </div>
                </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-warning"><i class='bx bx-group'></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">@php $data = \App\Setting\Dosen::all(); echo $data->count(); @endphp</h4>
                    </div>
                    <p class="mb-1">Dosen</p>
                    <p class="mb-0">
                        <span class="fw-medium me-1">Total dosen</span>
                    </p>
                    </div>
                </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-danger"><i class='bx bx-group'></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">@php $data = \App\Setting\Dekan::all(); echo $data->count(); @endphp</h4>
                    </div>
                    <p class="mb-1">Dekan</p>
                    <p class="mb-0">
                        <span class="fw-medium me-1">Total dekan</span>
                    </p>
                    </div>
                </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-info"><i class='bx bx-group'></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">@php $data = \App\Setting\Rektorat::all(); echo $data->count(); @endphp</h4>
                    </div>
                    <p class="mb-1">Rektorat</p>
                    <p class="mb-0">
                        <span class="fw-medium me-1">User Rektorat</span>
                    </p>
                    </div>
                </div>
            </div>
        </div>
    
    </div>
    <!-- / Content -->

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
        var table = $('#table_renang').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('data-dash-dekan') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'nama_jenis_kegiatan',name: 'nama_jenis_kegiatan'},
                {data: 'nama_prodi',name: 'nama_prodi'},
                {data: 'item',name: 'item'},
                {data: 'biaya_satuan',name: 'biaya_satuan'},
                {data: 'quantity',name: 'quantity'},
                {data: 'frequency',name: 'frequency'},
            ]
        });
    });
</script>
@endsection