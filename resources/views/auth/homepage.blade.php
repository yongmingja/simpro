<!DOCTYPE html>

<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Login {{config('app.name')}}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../images/svg/Aa.svg" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons -->
    <link rel="stylesheet" href="../../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../../assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/typeahead-js/typeahead.css" />
    <!-- Vendor -->
    <link rel="stylesheet" href="../../assets/vendor/libs/formvalidation/dist/css/formValidation.min.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="../../assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="../../assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../../assets/js/config.js"></script>
  </head>

  <body>
    <!-- Content -->

    <div class="authentication-wrapper authentication-cover">
      <div class="authentication-inner row m-0">
        <!-- /Left Text -->
        <div class="d-lg-flex col-lg-7 col-xl-12 align-items-center">
          <div class="text-center mx-auto">
            <img src="{{asset('assets/img/backgrounds/event.jpg')}}" alt="logo lpm" width="520" class="img-fluid authentication-cover-img">
            <!-- Login -->
              <div class="container-fluid col-sm-12 mb-3">
                  <div class="row row-cols-12">
                        <div class="col">
                          <div class="card shadow-none bg-transparent border border-primary" style="cursor: pointer;" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEndAdmin" aria-controls="offcanvasEnd">
                            <div class="card-body text-primary">
                              <p class="card-title">Admin</p>                    
                            </div>
                          </div>
                        </div>
                        <div class="col">
                          <div class="card shadow-none bg-transparent border border-secondary" style="cursor: pointer;" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEndMahasiswa" aria-controls="offcanvasEnd">
                            <div class="card-body text-secondary">
                              <p class="card-title">Mahasiswa</p>
                            </div>
                          </div>
                        </div>
                        <div class="col">
                          <div class="card shadow-none bg-transparent border border-success" style="cursor: pointer;" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEndDosen" aria-controls="offcanvasEnd">
                            <div class="card-body text-success">
                              <p class="card-title">Dosen</p>
                            </div>
                          </div>
                        </div>
                        <div class="col">
                          <div class="card shadow-none bg-transparent border border-warning" style="cursor: pointer;" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEndDekan" aria-controls="offcanvasEnd">
                            <div class="card-body text-warning">
                              <p class="card-title">Dekan</p>
                            </div>
                          </div>
                        </div>
                        <div class="col">
                          <div class="card shadow-none bg-transparent border border-danger" style="cursor: pointer;" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEndRekorat" aria-controls="offcanvasEnd">
                            <div class="card-body text-danger">
                              <p class="card-title">Rektorat</p>
                            </div>
                          </div>
                        </div>
                    </div>
          
              </div>
              <!-- /Login -->
            <div class="mx-auto mt-3">
              <h3>Salam Dunia Satu Keluarga</h3>
              <p>
                <b>SIMPRO (Sistem Informasi Pengajuan Proposal)</b><br>merupakan sistem yang dikembangkan untuk pengajuan proposal termasuk sarana prasarana yang ada di Universitas Universal.
              </p>
            </div>
          </div>
        </div>
        <!-- /Left Text -->

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEndAdmin" aria-labelledby="offcanvasEndLabel">          
          <div class="offcanvas-body my-auto mx-0 flex-grow-0">
            <form id="formAuthentication" class="mb-3" action="{{route('login.admin')}}" method="POST">
              <h5 id="offcanvasEndLabel" class="offcanvas-title mb-3">Login as Admin</h5>
              @csrf
              <div class="mb-3">
                <label for="email" class="form-label">Email or Username</label>
                <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Enter your email or username" autofocus />
              </div>
              <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                  <label class="form-label" for="password">Password</label>
                </div>
                <div class="input-group input-group-merge">
                  <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
              <button class="btn btn-primary d-grid w-100">
                Sign in
              </button>
            </form>
          </div>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEndMahasiswa" aria-labelledby="offcanvasEndLabel">          
          <div class="offcanvas-body my-auto mx-0 flex-grow-0">
            <form id="formAuthentication" class="mb-3" action="{{route('login.mahasiswa')}}" method="POST">
              <h5 id="offcanvasEndLabel" class="offcanvas-title mb-3">Login as Mahasiswa</h5>
              @csrf
              <div class="mb-3">
                <label for="email" class="form-label">Email or Username</label>
                <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Enter your email or username" autofocus />
              </div>
              <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                  <label class="form-label" for="password">Password</label>
                </div>
                <div class="input-group input-group-merge">
                  <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
              <button class="btn btn-primary d-grid w-100">
                Sign in
              </button>
            </form>
          </div>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEndDosen" aria-labelledby="offcanvasEndLabel">          
          <div class="offcanvas-body my-auto mx-0 flex-grow-0">
            <form id="formAuthentication" class="mb-3" action="{{route('login.dosen')}}" method="POST">
              <h5 id="offcanvasEndLabel" class="offcanvas-title mb-3">Login as Dosen</h5>
              @csrf
              <div class="mb-3">
                <label for="email" class="form-label">Email or Username</label>
                <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Enter your email or username" autofocus />
              </div>
              <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                  <label class="form-label" for="password">Password</label>
                </div>
                <div class="input-group input-group-merge">
                  <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
              <button class="btn btn-primary d-grid w-100">
                Sign in
              </button>
            </form>
          </div>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEndDekan" aria-labelledby="offcanvasEndLabel">          
          <div class="offcanvas-body my-auto mx-0 flex-grow-0">
            <form id="formAuthentication" class="mb-3" action="{{route('login.dekan')}}" method="POST">
              <h5 id="offcanvasEndLabel" class="offcanvas-title mb-3">Login as Dekan</h5>
              @csrf
              <div class="mb-3">
                <label for="email" class="form-label">Email or Username</label>
                <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Enter your email or username" autofocus />
              </div>
              <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                  <label class="form-label" for="password">Password</label>
                </div>
                <div class="input-group input-group-merge">
                  <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
              <button class="btn btn-primary d-grid w-100">
                Sign in
              </button>
            </form>
          </div>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEndRekorat" aria-labelledby="offcanvasEndLabel">          
          <div class="offcanvas-body my-auto mx-0 flex-grow-0">
            <form id="formAuthentication" class="mb-3" action="{{route('login.rektorat')}}" method="POST">
              <h5 id="offcanvasEndLabel" class="offcanvas-title mb-3">Login as Rektorat</h5>
              @csrf
              <div class="mb-3">
                <label for="email" class="form-label">Email or Username</label>
                <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Enter your email or username" autofocus />
              </div>
              <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                  <label class="form-label" for="password">Password</label>
                </div>
                <div class="input-group input-group-merge">
                  <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
              <button class="btn btn-primary d-grid w-100">
                Sign in
              </button>
            </form>
          </div>
        </div>
    
        
      </div>
    </div>
    
    <!-- / Content -->
    

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../../assets/vendor/libs/i18n/i18n.js"></script>
    <script src="../../assets/vendor/libs/typeahead-js/typeahead.js"></script>

    <script src="../../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../../assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="../../assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="../../assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>

    <!-- Main JS -->
    <script src="../../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../../assets/js/pages-auth.js"></script>
  </body>
</html>
