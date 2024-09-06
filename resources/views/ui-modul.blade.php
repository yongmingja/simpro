<!DOCTYPE html>
<html lang="en" class="dark-style layout-navbar-fixed layout-menu-fixed " dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="horizontal-menu-template-dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu Moduls</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/css/rtl/core-dark.css')}}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{asset('assets/vendor/fonts/boxicons.css')}}" />
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

</head>
<body>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-sm-12 mt-3">
                <div class="row p-3 border rounded-3">
                    <div class="col-sm-12">
                        <div class="container-fluid d-flex bg-body-tertiary rounded-3 mb-4 me-3 bg-primary flex-wrap justify-content-between py-5 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0 px-3">
                                <h1 class="display-6 fw-bold text-white">Universitas Universal</h1>
                                <p class="col-md-10 fs-5 text-white">Sistem Informasi Universitas Universal.</p>
                            </div>
                            <div class="px-3">                                
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" target="_blank" id="logoutButton">
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                <i class="bx bx-log-out me-2 text-white"></i>
                                <span class="align-middle text-white">Keluar</span>
                                </a>
                                
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <a href="{{route('home')}}">
                            <div class="card bg-warning text-white card-border-shadow-primary h-100">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3 rounded-3">
                                            <i class="bx bx-mail-send bx-lg mt-3"></i>
                                        </div>
                                        <div class="col-sm-9">
                                            <div class="d-flex align-items-center mb-2 pb-1">
                                                <h4 class="mb-0 fw-bold text-white">SIMPRO</h4>
                                            </div>
                                            <p class="mb-0">
                                                <span class="fw-medium me-1">Sistem Informasi<br>Pengajuan Proposal</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-4">                        
                        <a href="javascript:void()">
                            <div class="card border border-primary bg-transparent text-white card-border-shadow-primary h-100">
                                <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <h4 class="ms-1 mb-0 text-white">APPS 1</h4>
                                </div>
                                <p class="mb-0">
                                    <span class="fw-medium me-1">Sampel</span>
                                </p>
                                </div>
                            </div>
                        </a>                      
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <a href="javascript:void()">
                            <div class="card border border-success bg-transparent text-white card-border-shadow-primary h-100">
                                <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <h4 class="ms-1 mb-0 text-white">APPS 2</h4>
                                </div>
                                <p class="mb-0">
                                    <span class="fw-medium me-1">Sampel</span>
                                </p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <a href="javascript:void()">
                            <div class="card border border-info bg-transparent text-white card-border-shadow-primary h-100">
                                <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <h4 class="ms-1 mb-0 text-white">APPS 3</h4>
                                </div>
                                <p class="mb-0">
                                    <span class="fw-medium me-1">Sampel</span>
                                </p>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>

                <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-fluid d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0"><a href="https://uvers.ac.id/" target="_blank" class="footer-link fw-semibold">UVERS </a>
                                © <script>
                                document.write(new Date().getFullYear())
                                </script>
                                - All rights reserved 
                            </div>
                            <div>
                                
                                <a href="javascript:void(0)" class="footer-link me-4" target="_blank">License</a>
                                
                                <a href="javascript:void(0)" target="_blank" class="footer-link me-4">Documentation</a>
                                
                                
                                <a href="https://uvers.ac.id/" target="_blank" class="footer-link d-none d-sm-inline-block">Support</a>
                                
                            </div>
                        </div>
                    </footer>
                <!-- / Footer -->
            </div>
        </div>
    </div>

    {{-- @include('rsc.scripts') --}}
</body>
</html>