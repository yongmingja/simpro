<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

                
    <div class="app-brand demo mt-3">
    <a href="#" class="app-brand-link">
        <span class="app-brand-logo demo"><img src="{{asset('assets/img/icons/unicons/computer.png')}}" style="width:50px;" alt="logo"></span>
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
    @if(Auth::guard('admin')->user())
    <li class="menu-item">
        <a href="{{route('dashboard-admin')}}" class="menu-link {{set_active('dashboard-admin')}}">
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

    <li class="menu-header small fw-medium">
        <div data-i18n="DATABASE MASTER">DATABASE MASTER</div>
    </li>
    <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle {{set_active('data-user-admin.index')}} OR {{set_active('data-user-mahasiswa.index')}} OR {{set_active('data-user-dosen.index')}} OR {{set_active('data-user-dekan.index')}} OR {{set_active('data-user-rektorat.index')}}">
        <i class="menu-icon tf-icons bx bx-group bx-tada-hover"></i>
        <div data-i18n="Data User">Data User</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item">
                <a href="{{route('data-user-admin.index')}}" class="menu-link {{set_active('data-user-admin.index')}}">
                <div data-i18n="Data Admin">Data Admin</div>
                </a>
            </li>        
            <li class="menu-item">
                <a href="{{route('data-user-mahasiswa.index')}}" class="menu-link {{set_active('data-user-mahasiswa.index')}}">
                <div data-i18n="Data Mahasiswa">Data Mahasiswa</div>
                </a>
            </li>        
            <li class="menu-item">
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
            </li>
        </ul>
    </li>
    <li class="menu-item">
        <a href="{{route('data-jenis-kegiatan.index')}}" class="menu-link {{set_active('data-jenis-kegiatan.index')}}">
        <i class="menu-icon tf-icons bx bx-list-ol bx-tada-hover"></i>
        <div data-i18n="Jenis Proposal">Jenis Proposal</div>
        </a>
    </li>
    <li class="menu-item">
        <a href="{{route('data-fakultas.index')}}" class="menu-link {{set_active('data-fakultas.index')}}">
        <i class="menu-icon tf-icons bx bx-buildings bx-tada-hover"></i>
        <div data-i18n="Data Fakultas">Data Fakultas</div>
        </a>
    </li>
    <li class="menu-item">
        <a href="{{route('data-prodi.index')}}" class="menu-link {{set_active('data-prodi.index')}}">
        <i class="menu-icon tf-icons bx bx-category-alt bx-tada-hover"></i>
        <div data-i18n="Data Prodi">Data Prodi</div>
        </a>
    </li>

    <!-- Students Page -->
    @elseif(Auth::guard('mahasiswa')->user())
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

    <!-- Lecturer Page -->
    @elseif(Auth::guard('dosen')->user())
    <li class="menu-item">
        <a href="{{route('dashboard-dosen')}}" class="menu-link {{set_active('dashboard-dosen')}}">
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

    <!-- Dean Page -->
    @elseif(Auth::guard('dekan')->user())
    <li class="menu-item">
        <a href="{{route('dashboard-dekan')}}" class="menu-link {{set_active('dashboard-dekan')}}">
        <i class="menu-icon tf-icons bx bx-home-circle bx-tada-hover"></i>
        <div data-i18n="Dashboard">Dashboard</div>
        </a>
    </li>
    <li class="menu-item">
        <a href="{{route('page-data-proposal.index')}}" class="menu-link {{set_active('page-data-proposal.index')}}">
        <i class="menu-icon tf-icons bx bx-file bx-tada-hover"></i>
        <div data-i18n="Proposals">Proposals</div>
        </a>
    </li>

    <!-- Rector Page -->
    @elseif(Auth::guard('rektorat')->user())
    <li class="menu-item">
        <a href="{{route('dashboard-rektorat')}}" class="menu-link {{set_active('dashboard-rektorat')}}">
        <i class="menu-icon tf-icons bx bx-home-circle bx-tada-hover"></i>
        <div data-i18n="Dashboard">Dashboard</div>
        </a>
    </li>
    <li class="menu-item">
        <a href="{{route('index-hal-laporan')}}" class="menu-link {{set_active('index-hal-laporan')}}">
        <i class="menu-icon tf-icons bx bx-archive bx-tada-hover"></i>
        <div data-i18n="Laporan Proposal">Laporan Proposal</div>
        </a>
    </li>
    @else
    <li class="menu-item">
        <a href="#" class="menu-link">
        <i class="menu-icon tf-icons bx bx-group bx-tada-hover"></i>
        <div data-i18n="Belum ada menu">Belum ada menu</div>
        </a>
    </li>
    @endif
    
    </ul>

</aside>
<!-- / Menu -->

@push('after-scripts')

@endpush