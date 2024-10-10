@if (Auth::guard('pegawai')->user())
    @php
    $getjabatan         = \App\Models\Master\Jabatan::Class;
    $golonganPeg        = $getjabatan::where([['golongan_jabatan', 1]])->select('id','kode_jabatan','nama_jabatan')->get();
    $golonganAk         = $getjabatan::where([['golongan_jabatan', 2]])->select('id','kode_jabatan','nama_jabatan')->get();
    $user               = Auth::guard('pegawai')->user();
    $jabatanPegawai     = $user->jabatanPegawai()->orderBy('id_jabatan')->get();
    $jabatanAkademik    = $user->jabatanAkademik()->orderBy('id_jabatan')->get();
    $jabatanPegawaiIds  = $jabatanPegawai->pluck('id_jabatan')->all();
    $jabatanAkademikIds = $jabatanAkademik->pluck('id_jabatan')->all();
    $countRole          = count($jabatanPegawaiIds) + count($jabatanAkademikIds);
    $roleDefault        = null;

    if ($countRole == 1) {
        if (!empty($jabatanPegawaiIds)) {
            $getRole = $jabatanPegawaiIds[0];
            $jab = $getjabatan::where('id', $getRole)->select('kode_jabatan')->first();
            $roleDefault = $jab->kode_jabatan;
        } elseif (!empty($jabatanAkademikIds)) {
            $getRole = $jabatanAkademikIds[0];
            $jab = $getjabatan::where('id', $getRole)->select('kode_jabatan')->first();
            $roleDefault = $jab->kode_jabatan;
        }
    }
    elseif($countRole > 1) {
        if($jabatanPegawaiIds) {
            $getRole = $jabatanPegawaiIds[0];
            $jab = $getjabatan::where('id', $getRole)->select('kode_jabatan')->first();
            $roleDefault = $jab->kode_jabatan;
        } elseif ($jabatanAkademikIds) {
            $getRole = $jabatanAkademikIds[0];
            $jab = $getjabatan::where('id', $getRole)->select('kode_jabatan')->first();
            $roleDefault = $jab->kode_jabatan;
        }
    }

    if (!empty($selectedPeran)) {
    $roleDefault = $selectedPeran;
    }
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
                    <button class="btn btn-outline-warning" style="cursor:no-drop;" id="roleDefault">current-role as <b>{{ $roleDefault }}</b></button>
                    {{-- <input class="form-control" type="text" placeholder="Current-role as {{ $roleDefault }}" id="roleDefault" style="cursor:no-drop;"> --}}
                </li>
            @endif
            @if (Auth::guard('pegawai')->user() && $countRole > 1)
                @php 
                    $checkJabatanPeg = \App\Models\Master\JabatanPegawai::leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
                        ->where('jabatan_pegawais.id_pegawai',Auth::guard('pegawai')->user()->id)
                        ->select('jabatan_pegawais.id_jabatan','jabatans.kode_jabatan','jabatans.nama_jabatan')
                        ->get();

                    $checkJabatanAk = \App\Models\Master\jabatanAkademik::leftJoin('jabatans','jabatans.id','=','jabatan_akademiks.id_jabatan')
                        ->where('jabatan_akademiks.id_pegawai',Auth::guard('pegawai')->user()->id)
                        ->select('jabatan_akademiks.id_jabatan','jabatans.kode_jabatan','jabatans.nama_jabatan')
                        ->get();
                @endphp

                @if(count($jabatanPegawaiIds) > 0)

                <li class="nav-item">
                    <select class="select2 form-control" id="selectPeran">
                        @foreach($checkJabatanPeg as $japeg)
                        <option value="{{$japeg->kode_jabatan}}">{{$japeg->nama_jabatan}}</option>
                        @endforeach
                        @foreach($checkJabatanAk as $jaaka)
                        <option value="{{$jaaka->kode_jabatan}}">{{$jaaka->nama_jabatan}}</option>
                        @endforeach
                    </select>
                </li> 
                @endif
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
                            {{ Auth::guard('pegawai')->user()->nip }}
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
                {{-- <li>
                    <a class="dropdown-item" href="#">
                    <i class="bx bx-user me-2"></i>
                    <span class="align-middle">My Profile</span>
                    </a>
                </li> --}}
                <li>
                    {{-- <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" target="_blank" id="logoutButton">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    <i class="bx bx-power-off me-2"></i>
                    <span class="align-middle">Sign out</span>
                    </a> --}}

                    <a class="dropdown-item" href="{{ route('ui-modul') }}">
                    <i class="bx bx-log-out me-2"></i>
                    <span class="align-middle">Back to homepage</span>
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
    <a href="#" class="app-brand-link">
        <span class="app-brand-text demo menu-text fw-bold ms-2">SIMPRO</span>
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
        @if($roleDefault == "SADM" || $roleDefault == "ADU")
        <li class="menu-item">
            <a href="{{route('home')}}" class="menu-link {{set_active('home')}}">
            <i class="menu-icon tf-icons bx bx-home-circle bx-tada-hover"></i>
            <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('data-proposal.index')}}" class="menu-link {{set_active('data-proposal.index')}}">
            <i class="menu-icon tf-icons bx bx-file bx-tada-hover"></i>
            <div data-i18n="Proposals">Proposals</div>
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
                {{-- <li class="menu-item">
                    <a href="{{route('data-user-admin.index')}}" class="menu-link {{set_active('data-user-admin.index')}}">
                    <div data-i18n="Data Admin">Data Admin</div>
                    </a>
                </li>         --}}
                <li class="menu-item">
                    <a href="{{route('data-pegawai.index')}}" class="menu-link {{set_active('data-pegawai.index')}}">
                    <div data-i18n="Data Pegawai">Data Pegawai</div>
                    </a>
                </li>        
                <li class="menu-item">
                    <a href="{{route('data-user-mahasiswa.index')}}" class="menu-link {{set_active('data-user-mahasiswa.index')}}">
                    <div data-i18n="Data Mahasiswa">Data Mahasiswa</div>
                    </a>
                </li>        
                {{-- <li class="menu-item">
                    <a href="{{route('data-user-dosen.index')}}" class="menu-link {{set_active('data-user-dosen.index')}}">
                    <div data-i18n="Data Dosen & Tendik">Data Dosen & Tendik</div>
                    </a>
                </li>        
                <li class="menu-item">
                    <a href="{{route('data-user-dekan.index')}}" class="menu-link {{set_active('data-user-dekan.index')}}">
                    <div data-i18n="Data Dekan & Kepala Biro">Data Dekan & Kepala Biro</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{route('data-user-rektorat.index')}}" class="menu-link {{set_active('data-user-rektorat.index')}}">
                    <div data-i18n="Data Rektorat">Data Rektorat</div>
                    </a>
                </li> --}}
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{route('data-jenis-kegiatan.index')}}" class="menu-link {{set_active('data-jenis-kegiatan.index')}}">
            <i class="menu-icon tf-icons bx bx-list-ol bx-tada-hover"></i>
            <div data-i18n="Jenis Proposal">Jenis Proposal</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle {{set_active('data-fakultas.index')}} OR {{set_active('data-prodi.index')}}">
            <i class="menu-icon tf-icons bx bx-buildings bx-tada-hover"></i>
            <div data-i18n="Data Universitas">Data Universitas</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{route('data-fakultas.index')}}" class="menu-link {{set_active('data-fakultas.index')}}">
                    <div data-i18n="Fakultas">Fakultas</div>
                    </a>
                </li>        
                <li class="menu-item">
                    <a href="{{route('data-prodi.index')}}" class="menu-link {{set_active('data-prodi.index')}}">
                    <div data-i18n="Prodi">Prodi</div>
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
                    <a href="{{route('data-jabatan-akademik.index')}}" class="menu-link {{set_active('data-jabatan-akademik.index')}}">
                    <div data-i18n="Jabatan Akademik">Jabatan Akademik</div>
                    </a>
                </li> 
                <li class="menu-item">
                    <a href="{{route('data-jabatan-pegawai.index')}}" class="menu-link {{set_active('data-jabatan-pegawai.index')}}">
                    <div data-i18n="Jabatan Pegawai">Jabatan Pegawai</div>
                    </a>
                </li> 
            </ul>
        </li>
        @endif

        @if($roleDefault == "DSN")
        <li class="menu-item">
            <a href="{{route('submission-of-proposal.index')}}" class="menu-link {{set_active('submission-of-proposal.index')}} OR {{set_active('tampilan-proposal-baru')}}">
            <i class="menu-icon tf-icons bx bx-file bx-tada-hover"></i>
            <div data-i18n="Proposal Saya">Proposal Saya</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('my-report')}}" class="menu-link {{set_active('my-report')}} OR {{set_active('index-laporan')}}">
            <i class="menu-icon tf-icons bx bx-archive bx-tada-hover"></i>
            <div data-i18n="Laporan Saya">Laporan Saya</div>
            </a>
        </li>
        @endif

        @if($roleDefault == "DKN")
        <li class="menu-item">
            <a href="{{route('page-data-proposal.index')}}" class="menu-link {{set_active('page-data-proposal.index')}}">
            <i class="menu-icon tf-icons bx bx-file bx-tada-hover"></i>
            <div data-i18n="Proposals">Proposals</div>
            </a>
        </li>
        @endif

        @if($roleDefault == "WRAK")
        <li class="menu-item">
            <a href="{{route('dashboard-rektorat')}}" class="menu-link {{set_active('dashboard-rektorat')}}">
            <i class="menu-icon tf-icons bx bx-home-circle bx-tada-hover"></i>
            <div data-i18n="Proposal">Proposal</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('index-hal-laporan')}}" class="menu-link {{set_active('index-hal-laporan')}}">
            <i class="menu-icon tf-icons bx bx-archive bx-tada-hover"></i>
            <div data-i18n="Laporan Proposal">Laporan Proposal</div>
            </a>
        </li>
        @endif

    <!-- Students Page -->
    @elseif(Str::length(Auth::guard('mahasiswa')->user()) > 0)
        <li class="menu-item">
            <a href="{{route('dashboard-mahasiswa')}}" class="menu-link {{set_active('dashboard-mahasiswa')}}">
            <i class="menu-icon tf-icons bx bx-home-circle bx-tada-hover"></i>
            <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('submission-of-proposal.index')}}" class="menu-link {{set_active('submission-of-proposal.index')}} OR {{set_active('tampilan-proposal-baru')}}">
            <i class="menu-icon tf-icons bx bx-file bx-tada-hover"></i>
            <div data-i18n="Proposal Saya">Proposal Saya</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('my-report')}}" class="menu-link {{set_active('my-report')}} OR {{set_active('index-laporan')}}">
            <i class="menu-icon tf-icons bx bx-archive bx-tada-hover"></i>
            <div data-i18n="Laporan Saya">Laporan Saya</div>
            </a>
        </li>
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