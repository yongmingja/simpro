@extends('layouts.backend')
@section('title','Export Data Proposal')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('index-export-proposal')}}">@yield('title')</a>
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
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-body">
                        <form id="form-export-data" name="form-export-data" class="form-horizontal">
                            @csrf
                            <div class="row mb-3">
                                <div class="container">
                                    <div class="col-sm-12 mt-2">
                                        <div class="form-group mb-2">
                                            <label for="tahun_proposal" class="form-label">Pilih Tahun Akademik</label>
                                            <select class="select2 form-select" id="tahun_proposal" name="tahun_proposal" aria-label="Default select example" style="cursor:pointer;">
                                                <option value="" id="pilih_tahun">- Pilih -</option>
                                                <option value="[semua]" selected>Semua (default)</option>
                                                @foreach($checkYear as $years)
                                                <option value="{{ $years->id }}">{{ $years->year }}</option>
                                                @endforeach
                                            </select>
                                            <div class="form-text text-danger" id="tahunErrorMsg"></div>
                                        </div>
                                        <div class="form-group mb-2">
                                                <label for="filter_lembaga" class="form-label">Pilih Lembaga</label>
                                            <select style="cursor:pointer;" class="select2 form-control border" id="filter_lembaga" name="filter_lembaga" required>
                                                <option value="all" selected>Semua Lembaga (default)</option>                                    
                                                @foreach($getLembaga as $lembaga)
                                                    <option value="{{$lembaga->id}}">{{$lembaga->nama_fakultas_biro}}</option>
                                                @endforeach
                                                <option value="others">Lainnya (Rektorat)</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="filter_format" class="form-label">Format</label>
                                            <select class="select2 form-control" id="filter_format" name="filter_format" aria-label="Default select example" style="cursor:pointer;" onchange="display()">
                                                <option value="" id="choose_format">- Pilih -</option>
                                                <option value="HTML">HTML</option>
                                                <option value="Excel">Excel</option>
                                            </select>
                                            <span class="text-danger" id="groupNameErrorMsg"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form> 
                        <div class="row">
                            <div class="container">
                                <div class="col-sm-10">
                                    <div class="col-sm-offset-2 mb-3">
                                        <div>
                                            <a href="{{ route('show-data-proposal-html',['year' => 0, 'lembaga' => 0]) }}" id="preview-html" class="btn btn-primary d-none" target="_blank"><i class="bx bx-windows"></i> Tampilkan tab baru</a>
                                            <a href="{{ route('download-proposal-excel', ['year' => 0, 'lembaga' => 0]) }}" target="_blank" id="download-excel" class="btn btn-success d-none"><i class="bx bx-file"></i> Download Excel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                           
                    </div>
                </div>    
            </div>
            <div class="col-sm-9">
                <div class="card table-responsive">
                    <div class="card-body">
                        <table class="table table-hover table-responsive" id="table-proposal">
                            <thead>
                                <th>#</th>
                                <th>Tahun Akademik</th>
                                <th>Judul Proposal</th>
                                <th>Tgl Kegiatan</th>
                                <th>Tgl Pengajuan</th>
                                <th>Ketua Pelaksana</th>
                                <th>Status Laporan</th>
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
        fill_datatable();
        function fill_datatable(tahun_proposal = '',filter_lembaga = 'all'){
            var table = $('#table-proposal').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('index-export-proposal') }}",
                    "type": "GET",
                    "data": function(data){
                        data.tahun_proposal = $('#tahun_proposal').val();
                        data.filter_lembaga = $('#filter_lembaga').val();
                    }
                },
                columns: [
                    {data: null,sortable:false,
                        render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, 
                    {data: 'year',name: 'year'},
                    {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                    {data: 'tgl_event',name: 'tgl_event',
                        render: function (data, type, row) {
                            return moment(row.tgl_event).format("DD MMM YYYY")
                        }
                    },
                    {data: 'created_at',name: 'created_at',
                        render: function (data, type, row) {
                            return moment(row.created_at).format("DD MMM YYYY")
                        }
                    },
                    {data: 'nama_pegawai',name: 'nama_pegawai'},
                    {data: 'status',name: 'status'},
                ]
            });
        }
        $('#tahun_proposal, #filter_lembaga').on('change', function(e){
            var tahun_proposal = this.value;
            var selectLembaga = this.value;

            if(tahun_proposal != '' || selectLembaga != ''){
                $('#table-proposal').DataTable().destroy();
                fill_datatable(tahun_proposal, selectLembaga);
            } else {
                alert('Anda belum memilih tahun.');
                $('#table-proposal').DataTable().destroy();
                fill_datatable();
            }
        });
    });

    function display(){
        var formatSelected = $('#filter_format').val();
        if(formatSelected == 'HTML'){
            $('#preview-html').removeClass('d-none');            
            $('#download-excel').addClass('d-none');            
        } else {
            $('#preview-html').addClass('d-none');            
            $('#download-excel').removeClass('d-none');            
        }
    }

    $('#preview-html').on('click', function () {
        let filterYear = $('#tahun_proposal').val();
        let filterLembaga = $('#filter_lembaga').val();
        let reviewHtmlLink = "{{ route('show-data-proposal-html', ['year' => ':getyear', 'lembaga' => ':getlembaga']) }}";
        // Mengganti placeholder dengan nilai aktual
        reviewHtmlLink = reviewHtmlLink.replace(':getyear', filterYear).replace(':getlembaga', filterLembaga);
        // Mengatur href elemen anchor (a) dengan link yang telah diperbarui
        $('#preview-html').attr('href', reviewHtmlLink);
    });

    $('#download-excel').on('click', function () {
        let filterYear = $('#tahun_proposal').val();
        let filterLembaga = $('#filter_lembaga').val();
        let reviewExcelLink = "{{ route('download-proposal-excel', ['year' => ':getyear', 'lembaga' => ':getlembaga']) }}";
        // Mengganti placeholder dengan nilai aktual
        reviewExcelLink = reviewExcelLink.replace(':getyear', filterYear).replace(':getlembaga', filterLembaga);
        // Mengatur href elemen anchor (a) dengan link yang telah diperbarui
        $('#download-excel').attr('href', reviewExcelLink);
    });

    $('#pilih_tahun').attr('disabled','disabled');

</script>

@endsection