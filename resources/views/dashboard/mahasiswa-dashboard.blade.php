@extends('layouts.backend')
@section('title','Dashboard')

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Card Border Shadow -->
        <div class="row">
            <h5>Hallo, {{Auth::user()->name}}</h5>
            <h4>Selamat datang di Dashboard Sistem Pengajuan Proposal</h4>
            <p>Anda memiliki @php $count = DB::table('proposals')->where('user_id',Auth::user()->user_id)->count(); @endphp <span class="badge bg-label-warning">{{$count}}</span> proposal. Anda dapat mengajukan proposal pada menu <a href="{{route('submission-of-proposal.index')}}">Proposal Saya</a></p>
        </div>
        <!--/ Card Border Shadow -->
    
    </div>
    <!-- / Content -->

@endsection
@section('script')
@endsection