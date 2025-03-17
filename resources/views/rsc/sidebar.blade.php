@if (Auth::guard('pegawai')->user())
    @php
        $getjabatan         = \App\Models\Master\Jabatan::Class;
        $golonganPeg        = $getjabatan::select('id','kode_jabatan','nama_jabatan')->get();
        $user               = Auth::guard('pegawai')->user();
        $jabatanPegawai     = $user->jabatanPegawai()->orderBy('id_jabatan')->get();
        $jabatanPegawaiIds  = $jabatanPegawai->pluck('id_jabatan')->all();
        $countRole          = count($jabatanPegawaiIds);
        $roleDefault        = null;

        if($countRole > 0) {
            if($jabatanPegawaiIds) {
                $getRole = $jabatanPegawaiIds[0];
                $jab = $getjabatan::where('id', $getRole)->select('kode_jabatan')->first();
                $roleDefault = $jab->kode_jabatan;
            } 
        }

        if (!empty($selectedPeran)) {
        $roleDefault = $selectedPeran;
        }

        # Check Validators: Dinamically Sidebar Menu
        $getModelValidator = \App\Models\Master\ValidatorProposal::Class;
        $getModelJabatanPegawai = \App\Models\Master\JabatanPegawai::Class;
        $getModelHandleProposal = \App\Models\Master\HandleProposal::Class;
        $getModelFormRkat = \App\Models\Master\FormRkat::Class;
        
        $getPeran = $getModelJabatanPegawai::leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
            ->where('jabatan_pegawais.id_pegawai',Auth::user()->id)
            ->select('jabatans.kode_jabatan','jabatan_pegawais.ket_jabatan')
            ->first();

            if(session()->get('selected_peran') == null){
                $recentPeranIs = $getPeran->kode_jabatan;
                $recentPeranKet = $getPeran->ket_jabatan;
            } else {
                $recentPeranIs = session()->get('selected_peran');
            }
        $checkIDValidatorPengusul = $getModelValidator::leftJoin('jabatans','jabatans.id','=','validator_proposals.diusulkan_oleh')
            ->select('jabatans.kode_jabatan')
            ->where('jabatans.kode_jabatan',$recentPeranIs)
            ->get();
        $checkIDValidatorDiketahui = $getModelValidator::leftJoin('jabatans','jabatans.id','=','validator_proposals.diketahui_oleh')
            ->select('jabatans.kode_jabatan')
            ->where('jabatans.kode_jabatan',$recentPeranIs)
            ->get();

        $totalData = $getModelFormRkat::where('status_validasi',0)->count();

    @endphp
@elseif(Auth::guard('mahasiswa')->user())
    
@endif


<!-- Navbar -->
<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="container-fluid"> 
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0   d-xl-none ">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        <ul class="navbar-nav flex-row align-items-center ms-auto"> 
            
            <!-- Switching role -->
            @if (Auth::guard('pegawai')->user() && $countRole == 1)
                <li class="mt-1 nav-item me-2 me-xl-0">
                    @if($recentPeranKet != null)
                    <button class="btn btn-outline-warning" style="cursor:no-drop;" id="roleDefault">current-role as <b>{{ $recentPeranKet }}</b></button>
                    @else
                    <button class="btn btn-outline-warning" style="cursor:no-drop;" id="roleDefault">current-role as <b>{{ $recentPeranIs }}</b></button>
                    @endif
                </li>
            @elseif(Auth::guard('pegawai')->user() && $countRole > 1)
                @php 
                    $checkJabatanPeg = \App\Models\Master\JabatanPegawai::leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
                        ->where('jabatan_pegawais.id_pegawai',Auth::guard('pegawai')->user()->id)
                        ->select('jabatan_pegawais.id_jabatan','jabatan_pegawais.ket_jabatan','jabatans.kode_jabatan','jabatans.nama_jabatan')
                        ->orderBy('jabatans.nama_jabatan','ASC')
                        ->get();
                @endphp

                <li class="nav-item">
                    <select class="select2 form-control" id="selectPeran">
                        @foreach($checkJabatanPeg as $japeg)
                        @if($japeg->ket_jabatan != null)
                        <option {{$japeg->kode_jabatan == $recentPeranIs ? 'selected' : ''}} value="{{$japeg->kode_jabatan}}">{{$japeg->ket_jabatan}}</option>
                        @else
                        <option {{$japeg->kode_jabatan == $recentPeranIs ? 'selected' : ''}} value="{{$japeg->kode_jabatan}}">{{$japeg->nama_jabatan}}</option>
                        @endif
                        @endforeach
                    </select>
                </li> 
            @endif
            <!-- / Switching role -->

            <!-- Style Switcher -->
            <li class="nav-item me-2 me-xl-0">
                <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
                <i class='bx bx-sm'></i>
                </a>
            </li>
            <!--/ Style Switcher -->        

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    <img src="{{asset('assets/img/avatars/22.png')}}" alt class="rounded-circle">
                </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="#">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                        <div class="avatar avatar-online">
                            <img src="{{asset('assets/img/avatars/22.png')}}" alt class="rounded-circle">
                        </div>
                        </div>
                        <div class="flex-grow-1">
                        <span class="fw-semibold d-block lh-1">@if (Str::length(Auth::guard('pegawai')->user()) > 0 )
                            {{ Auth::guard('pegawai')->user()->nama_pegawai }}
                            @elseif(Str::length(Auth::guard('mahasiswa')->user()) > 0)
                            {{ Auth::guard('mahasiswa')->user()->name }}
                            @endif</span>
                        <small>@if (Str::length(Auth::guard('pegawai')->user()) > 0 )
                            {{ Auth::guard('pegawai')->user()->user_id }}
                            @elseif(Str::length(Auth::guard('mahasiswa')->user()) > 0)
                            {{ Auth::guard('mahasiswa')->user()->user_id }}
                            @endif</small>
                        </div>
                    </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                <li>
                    <a class="dropdown-item" href="{{route('profile')}}">
                    <i class="bx bx-user me-2"></i>
                    <span class="align-middle">My Profile</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" target="_blank" id="logoutButton">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    <i class="bx bx-power-off me-2"></i>
                    <span class="align-middle">Sign out</span>
                    </a>
                </li>
                </ul>
            </li>
            <!--/ User -->        

        </ul>
    </div>

    
    <!-- Search Small Screens -->
    <div class="navbar-search-wrapper search-input-wrapper  d-none">
        <input type="text" class="form-control search-input container-fluid border-0" placeholder="Search..." aria-label="Search...">
        <i class="bx bx-x bx-sm search-toggler cursor-pointer"></i>
    </div>
    
    
    </div>
</nav>

<!-- / Navbar -->

<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">   
    <div class="app-brand demo mt-3">
    <a href="{{route('home')}}" class="app-brand-link">
        <span class="app-brand-logo demo"><img src="{{asset('assets/img/logo-uvers.png')}}" style="width:100px;" alt="logolpm"></span>
        <span class="app-brand-text demo menu-text fw-bold ms-2">{{config('app.name')}}</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
        <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
    </a>
    </div>

    
    <div class="menu-divider mt-0">
    </div>

    <div class="menu-inner-shadow"></div>

    
    
    <ul class="menu-inner py-1">

    <li class="menu-header small fw-medium">
        <div data-i18n="MENUS">MENUS</div>
    </li>


    <!-- Admin Page -->
    @if(Str::length(Auth::guard('pegawai')->user()) > 0)    
        <li class="menu-item">
            <a href="{{route('home')}}" class="menu-link {{set_active('home')}}">
            <i class="menu-icon tf-icons bx bx-home-circle bx-tada-hover"></i>
            <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        @if($roleDefault == "SADM" || $roleDefault == "ADU")
        <li class="menu-item">
            <a href="{{route('data-proposal.index')}}" class="menu-link {{set_active('data-proposal.index')}}">
            <i class="menu-icon tf-icons bx bx-file bx-tada-hover"></i>
            <div data-i18n="Validasi Proposal">Validasi Proposal</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('data-fpku.index')}}" class="menu-link {{set_active('data-fpku.index')}}">
            <i class="menu-icon tf-icons bx bx-plus-circle bx-tada-hover"></i>
            <div data-i18n="Buat FPKU">Buat FPKU</div>
            </a>
        </li>
        @endif

        @if($roleDefault == "SADM")
            <li class="menu-header small fw-medium">
                <div data-i18n="DATABASE MASTER">DATABASE MASTER</div>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle {{set_active('data-user-admin.index')}} OR {{set_active('data-user-mahasiswa.index')}} OR {{set_active('data-user-dosen.index')}} OR {{set_active('data-user-dekan.index')}} OR {{set_active('data-user-rektorat.index')}} OR {{set_active('data-pegawai.index')}}">
                <i class="menu-icon tf-icons bx bx-group bx-tada-hover"></i>
                <div data-i18n="Data User">Data User</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{route('data-pegawai.index')}}" class="menu-link {{set_active('data-pegawai.index')}}">
                        <div data-i18n="Data Pegawai">Data Pegawai</div>
                        </a>
                    </li> 
                </ul>
            </li>
                
            
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle {{set_active('data-jabatan.index')}} OR {{set_active('data-jabatan-akademik.index')}} OR {{set_active('data-jabatan-pegawai.index')}}">
                <i class="menu-icon tf-icons bx bx-sitemap bx-tada-hover"></i>
                <div data-i18n="Data Jabatan">Data Jabatan</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{route('data-jabatan.index')}}" class="menu-link {{set_active('data-jabatan.index')}}">
                        <div data-i18n="Jabatan">Jabatan</div>
                        </a>
                    </li>  
                    <li class="menu-item">
                        <a href="{{route('data-jabatan-pegawai.index')}}" class="menu-link {{set_active('data-jabatan-pegawai.index')}}">
                        <div data-i18n="Jabatan Pegawai">Jabatan Pegawai</div>
                        </a>
                    </li> 
                </ul>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle {{set_active('data-fakultas-biro.index')}} OR {{set_active('data-prodi-biro.index')}} OR {{set_active('validator-proposal.index')}} OR {{set_active('handle-proposal.index')}} OR {{set_active('data-jenis-kegiatan.index')}}">
                <i class="menu-icon tf-icons bx bx-data bx-tada-hover"></i>
                <div data-i18n="Data Lainnya">Data Lainnya</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{route('data-fakultas-biro.index')}}" class="menu-link {{set_active('data-fakultas-biro.index')}}">
                        <div data-i18n="Fakultas & Biro">Fakultas & Biro</div>
                        </a>
                    </li>        
                    <li class="menu-item">
                        <a href="{{route('data-prodi-biro.index')}}" class="menu-link {{set_active('data-prodi-biro.index')}}">
                        <div data-i18n="Prodi & Biro">Prodi & Biro</div>
                        </a>
                    </li> 
                    <li class="menu-item">
                        <a href="{{route('validator-proposal.index')}}" class="menu-link {{set_active('validator-proposal.index')}}">
                        <div data-i18n="Validator Proposal">Validator Proposal</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{route('handle-proposal.index')}}" class="menu-link {{set_active('handle-proposal.index')}}">
                        <div data-i18n="Handle Proposal">Handle Proposal</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{route('data-jenis-kegiatan.index')}}" class="menu-link {{set_active('data-jenis-kegiatan.index')}}">
                        <div data-i18n="Kategori Proposal">Kategori Proposal</div>
                        </a>
                    </li> 
                </ul>
            </li>
        @endif

        @if($checkIDValidatorPengusul->count() > 0)
            @foreach($checkIDValidatorPengusul as $idvalidator)
                @if($idvalidator->kode_jabatan == $recentPeranIs)
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle {{set_active('submission-of-proposal.index')}} OR {{set_active('tampilan-proposal-baru')}} OR {{set_active('my-report')}} OR {{set_active('index-laporan')}}">
                    <i class="menu-icon tf-icons bx bx-plus-circle bx-tada-hover"></i>
                    <div data-i18n="Buat Proposals">Buat Proposals</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{route('submission-of-proposal.index')}}" class="menu-link {{set_active('submission-of-proposal.index')}} OR {{set_active('tampilan-proposal-baru')}}">
                            <div data-i18n="Proposal Saya">Proposal Saya</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{route('my-report')}}" class="menu-link {{set_active('my-report')}} OR {{set_active('index-laporan')}}">
                            <div data-i18n="Laporan Saya">Laporan Saya</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle {{set_active('undangan-fpku')}} OR {{set_active('index-laporan-fpku')}} OR {{set_active('buat-laporan-fpku')}}">
                    <i class="menu-icon tf-icons bx bx-mail-send bx-tada-hover"></i>
                    <div data-i18n="Undangan FPKU">Undangan FPKU</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{route('undangan-fpku')}}" class="menu-link {{set_active('undangan-fpku')}}">
                            <div data-i18n="Undangan">Undangan</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{route('index-laporan-fpku')}}" class="menu-link {{set_active('index-laporan-fpku')}} OR {{set_active('buat-laporan-fpku')}}">
                            <div data-i18n="Laporan FPKU">Laporan FPKU</div>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
            @endforeach        
        @endif

        <li class="menu-item">
            <a href="{{route('form-rkat.index')}}" class="menu-link {{set_active('form-rkat.index')}}">
            <i class="menu-icon tf-icons bx bx-list-plus bx-tada-hover"></i>
            <div data-i18n="Buat RKAT">Buat RKAT</div>
            </a>
        </li> 

        @if($checkIDValidatorDiketahui->count() > 0)
            @foreach($checkIDValidatorDiketahui as $idvalidator)
                @if($idvalidator->kode_jabatan == $recentPeranIs)
                <li class="menu-header small fw-medium">
                    <div data-i18n="MENU VALIDASI">MENU VALIDASI</div>
                </li> 
                    <li class="menu-item">
                        <a href="{{route('page-data-proposal.index')}}" class="menu-link {{set_active('page-data-proposal.index')}}">
                        <i class="menu-icon tf-icons bx bx-file bx-tada-hover"></i>
                        <div data-i18n="Proposals">Proposals</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{route('page-laporan-proposal.index')}}" class="menu-link {{set_active('page-laporan-proposal.index')}}">
                        <i class="menu-icon tf-icons bx bx-folder bx-tada-hover"></i>
                        <div data-i18n="Laporan Proposal">Laporan Proposal</div>
                        </a>
                    </li>
                @endif
            @endforeach        
        @endif

        <!-- Check if peran match and user handle the proposals -->
        @if($roleDefault == "WAREK") 
            <li class="menu-header small fw-medium">
                <div data-i18n="VALIDASI REKTORAT">VALIDASI REKTORAT</div>
            </li> 
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle {{set_active('dashboard-rektorat')}} OR {{set_active('index-hal-laporan')}}">
                <i class="menu-icon tf-icons bx bx-file bx-tada-hover"></i>
                <div data-i18n="Validasi Proposals">Validasi Proposals</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{route('dashboard-rektorat')}}" class="menu-link {{set_active('dashboard-rektorat')}}">
                        <div data-i18n="Proposal">Proposal</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{route('index-hal-laporan')}}" class="menu-link {{set_active('index-hal-laporan')}}">
                        <div data-i18n="Laporan Proposal">Laporan Proposal</div>
                        </a>
                    </li>
                </ul>
            </li> 
            @if($roleDefault == "WAREK" AND $recentPeranKet == "WRSDP")
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle {{set_active('rundanganfpku')}} OR {{set_active('rlaporanfpku')}}">
                    <i class="menu-icon tf-icons bx bx-user-check bx-tada-hover"></i>
                    <div data-i18n="Validasi FPKU">Validasi FPKU</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{route('rundanganfpku')}}" class="menu-link {{set_active('rundanganfpku')}}">
                            <div data-i18n="Undangan">Undangan</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{route('rlaporanfpku')}}" class="menu-link {{set_active('rlaporanfpku')}}">
                            <div data-i18n="Laporan FPKU">Laporan FPKU</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item">
                    <a href="{{route('index-form-rkat')}}" class="menu-link {{set_active('index-form-rkat')}}">
                    <i class="menu-icon tf-icons bx bx-spreadsheet bx-tada-hover"></i>
                    <div data-i18n="Validasi Form RKAT">Validasi Form RKAT</div>
                    <div class="badge @if($totalData == 0) bg-success @else bg-danger @endif rounded-pills ms-auto" style="border-radius: 15px;">{{$totalData}}</div>
                    </a>
                    
                </li> 
            @endif 
            <li class="menu-header small fw-medium">
                <div data-i18n="MENU LAINNYA">MENU LAINNYA</div>
            </li> 
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle {{set_active('index-monitoring-proposals')}} OR {{set_active('index-monitoring-laporan-proposals')}}">
                <i class="menu-icon tf-icons bx bx-search bx-tada-hover"></i>
                <div data-i18n="Monitor Proposal">Monitor Proposal</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{route('index-monitoring-proposals')}}" class="menu-link {{set_active('index-monitoring-proposals')}}">
                        <div data-i18n="Proposal">Proposal</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{route('index-monitoring-laporan-proposals')}}" class="menu-link {{set_active('index-monitoring-laporan-proposals')}}">
                        <div data-i18n="Laporan Proposal">Laporan Proposal</div>
                        </a>
                    </li>
                </ul>
            </li> 
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle {{set_active('index-export-proposal')}} OR {{set_active('index-export-fpku')}}">
                <i class="menu-icon tf-icons bx bx-export bx-tada-hover"></i>
                <div data-i18n="Export Data">Export Data</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{route('index-export-proposal')}}" class="menu-link {{set_active('index-export-proposal')}}">
                        <div data-i18n="Proposal & Laporan">Proposal & Laporan</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{route('index-export-fpku')}}" class="menu-link {{set_active('index-export-fpku')}}">
                        <div data-i18n="FPKU">FPKU</div>
                        </a>
                    </li>
                </ul>
            </li> 
        @endif        
    @endif
    
    </ul>

</aside>
<!-- / Menu -->

@push('after-scripts')
<script>
    // Inisialisasi Select2 pada elemen select
    $(document).ready(function () {
        $('#selectPeran').select2();
        var selectPeran = $('#selectPeran');
        var selectDef = $('#roleDefault');
        var selectedTrigger = localStorage.getItem('selectedPeran'); // Ambil dari localStorage

        // Set selected value dari localStorage jika ada
        if (selectedTrigger) {
            selectPeran.val(selectedTrigger).trigger('change'); // Trigger event 'change'
        } 

        // Menambahkan event listener select2
        $('#selectPeran').on('select2:select', function (e) {
            // Mendapatkan nilai
            var selectedValue = e.params.data.id;
            localStorage.setItem('selectedPeran', selectedValue); // Simpan di localStorage
            $.ajax({
                url: "{{ route('ubah-peran') }}",
                method: "POST",
                data: {
                    peran: selectedValue,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    var url = "{{ route('home') }}";
                    window.location.href = url;
                },
                error: function (error) {
                    console.error(error);
                }
            });
        });

        var roleDefault = selectDef.val();
        if (roleDefault) {
            var selectedDefault = localStorage.getItem('selectedPeran'); // Ambil dari localStorage
            selectDef.val(selectedDefault).trigger('change'); // Trigger event 'change'
            $.ajax({
                url: "{{ route('ubah-peran') }}",
                method: "POST",
                data: {
                    peran: roleDefault,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    // Tangani respons dari server jika diperlukan
                    console.log(response);
                },
                error: function (error) {
                    // Tangani kesalahan jika diperlukan
                    console.error(error);
                }
            });
        }

        $('#logoutButton').on('click', function () {
            // Hapus semua data dari localStorage saat logout
            localStorage.removeItem('selectedPeran');
        });
    });

</script>
@endpush