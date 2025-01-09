@extends('layouts.backend')
@section('title','Dashboard')

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <h5>Hallo, {{Auth::user()->name}}</h5>
            <h4>Selamat datang di Dashboard Sistem Pengajuan Proposal Kegiatan</h4>
            <p>Anda memiliki @php $count = DB::table('proposals')->leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')->where([['proposals.id_fakultas',Auth::guard('pegawai')->user()->id_fakultas],['status_proposals.status_approval','=',1]])->count(); @endphp <span class="badge bg-label-warning">{{$count}}</span> proposal untuk diperiksa. Anda dapat melihat proposal pada menu <a href="{{route('page-data-proposal.index')}}">Proposals</a></p>
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