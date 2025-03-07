@extends('layouts.backend')
@section('title','Profile')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="#">@yield('title')</a>
      </li>
      <li class="breadcrumb-item active">Data</li>
    </ol>
</nav>
</div>
@endsection

@section('content')
<hr class="mt-2">
<div class="container-fluid flex-grow-1">
    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible" role="alert" id="success-alert">
            {{Session::get('success')}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
    @endif
    <div class="row">
        @if($datas->count() > 0)
          @foreach($datas as $em)
            @if($em->email == null)
              <div class="col-sm-12">
                <h4 class="text-warning"><i class="bx bx-error text-warning bx-tada bx-sm"></i> Silahkan lengkapi data alamat email Anda <div class="spinner-grow spinner-grow-sm text-warning" role="status"><span class="visually-hidden"></span></div></h4>
              </div>
            @endif
          @endforeach
        @endif        
        <div class="col-sm-3">
            <div class="card text-center">
                <img src="{{asset('assets/img/avatars/22.png')}}" class="card-img-top rounded-circle mx-auto d-block mt-4 img-fluid img-thumbnail" alt="user-image" style="width: 10rem;">
                <div class="card-body">
                  <h5 class="card-title mb-0">{{Auth::user()->nama_pegawai}}{{Auth::user()->name}}</h5>
                  <p class="card-text">{{Auth::user()->email}}</p>
                </div>
              </div>
        </div>
        <div class="col-sm-9">
            <div class="nav-align-top">
                <ul class="nav nav-tabs" role="tablist" id="tabMenu">
                  <li class="nav-item">
                    <a class="nav-link active" role="tab" data-toggle="tab" href="#home" aria-selected="true">Info</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" role="tab" data-toggle="tab" href="#changePassword" aria-selected="false">Change Password</a>
                  </li>
                  {{-- <li class="nav-item">
                    <a class="nav-link" role="tab" data-toggle="tab" href="#messages" aria-selected="false">Messages</a>
                  </li> --}}
                </ul>

                <div class="tab-content">
                  <div class="tab-pane fade show active" id="home" role="tabpanel">
                    <p class="mb-0">
                      <table class="table table-borderless table-hover table-sm">
                        @if(count($datas))
                          @foreach($datas as $data)
                          <tr>
                            <td width="15%;">Full Name</td>
                            <td>:&nbsp;{{$data->nama_pegawai}}{{$data->name}}</td>
                          </tr>
                          <tr>
                            <td width="15%;">NIP</td>
                            <td>:&nbsp;{{$data->user_id}}</td>
                          </tr>
                          <tr>
                            <td width="13%;">Email</td>
                            <td>:&nbsp;{{$data->email}} &nbsp;<a href="javascript:void()" data-bs-toggle="tooltip" id="update-email" data-bs-placement="bottom" title="Update email" data-bs-original-title="Update email"><i class="bx bx-edit bx-xs"></i></a></td>
                          </tr>
                          <tr>
                            <td width="13%;">Tgl. Lahir</td>
                            <td>:&nbsp;{{date('d M Y',strtotime($data->tanggal_lahir))}}</td>
                          </tr>
                          <tr>
                            <td width="13%;">Agama</td>
                            <td>:&nbsp;{{$data->agama}}</td>
                          </tr>
                          @endforeach
                        @else
                          <tr>
                            <td width="13%;">Full Name</td>
                            <td>:&nbsp;{{Auth::user()->name}}</td>
                          </tr>
                          <tr>
                            <td width="13%;">NIP</td>
                            <td>:&nbsp;&mdash;</td>
                          </tr>
                          <tr>
                            <td width="13%;">Email</td>
                            <td>:&nbsp;{{Auth::user()->email}}</td>
                          </tr>
                          <tr>
                            <td width="13%;">Phone Number</td>
                            <td>:&nbsp;&mdash;</td>
                          </tr>
                        @endif
                      </table>
                    </p>
                  </div>
                  <div class="tab-pane fade" id="changePassword" role="tabpanel">
                    <form method="POST" action="{{ route('change-password') }}">
                      @csrf 
                      <div class="form-group row mb-3">
                          <label for="password" class="col-sm-3 col-form-label text-md-right">Current Password</label>
                          <div class="col-md-4">
                              <input id="password" type="password" class="form-control {{$errors->has('current_password') ? 'has-error' : ''}}" name="current_password" autocomplete="current-password" value="{{ old('current_password') }}">
                              @error('current_password')
                                <div class="alert-danger mt-2">{{$errors->first('current_password') }} </div>
                              @enderror
                          </div>
                      </div>

                      <div class="form-group row mb-3">
                          <label for="password" class="col-sm-3 col-form-label text-md-right">New Password</label>

                          <div class="col-md-4">
                              <input id="new_password" minlength="6" type="password" class="form-control {{$errors->has('new_password') ? 'has-error' : ''}}" name="new_password" autocomplete="current-password" value="{{ old('new_password') }}">
                              @error('new_password')
                                <div class="alert-danger mt-2">{{$errors->first('new_password') }} </div>
                              @enderror
                          </div>
                      </div>

                      <div class="form-group row mb-3">
                          <label for="password" class="col-sm-3 col-form-label text-md-right">New Confirm Password</label>
  
                          <div class="col-md-4">
                              <input id="new_confirm_password" type="password" class="form-control {{$errors->has('new_confirm_password') ? 'has-error' : ''}}" name="new_confirm_password" autocomplete="current-password" value="{{ old('new_confirm_password') }}">
                              @error('new_confirm_password')
                                <div class="alert-danger mt-2">{{$errors->first('new_confirm_password') }} </div>
                              @enderror
                          </div>
                      </div>
 
                      <div class="form-group row mb-0">
                          <div class="col-md-8 offset-sm-3">
                              <button type="submit" class="btn btn-primary">
                                  Update
                              </button>
                          </div>
                      </div>
                  </form>
                  </div>
                  {{-- <div class="tab-pane fade" id="messages" role="tabpanel">
                    <p>
                      Oat cake chupa chups dragée donut toffee. Sweet cotton candy jelly beans macaroon gummies cupcake gummi
                      bears
                      cake chocolate.
                    </p>
                    <p class="mb-0">
                      Cake chocolate bar cotton candy apple pie tootsie roll ice cream apple pie brownie cake. Sweet roll icing
                      sesame snaps caramels danish toffee. Brownie biscuit dessert dessert. Pudding jelly jelly-o tart brownie
                      jelly.
                    </p>
                  </div> --}}
                </div>
            </div>
        </div>
        {{-- Modal Update email --}}
        <div class="modal fade" id="update-email-modal" aria-hidden="true">
          <div class="modal-dialog ">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="modal-judul"></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form action="{{ route('update-email-address') }}" method="POST">
                      @csrf
                          <div class="row">
                              <input type="hidden" id="id" name="id">
                              <div class="mb-3">
                                  <label for="update_email" class="form-label">Masukkan email aktif</label>
                                  <input type="email" class="form-control" id="update_email" name="update_email" value="" autofocus />
                                  <span class="text-danger" id="emailErrorMsg" style="font-size: 10px;"></span>
                              </div>                                         
                              
                              <div class="col-sm-offset-2 col-sm-12">
                                  <hr class="mt-2">
                                  <div class="float-sm-end">
                                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                      <button type="submit" class="btn btn-primary btn-block" id="tombol-simpan" value="create">Save</button>
                                  </div>
                              </div>
                          </div>

                      </form>
                  </div>
                  <div class="modal-footer">
                  </div>
              </div>
          </div>
        </div>
        {{-- Eof Modal update email --}}
    </div>
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

    $(document).ready(function(){
      $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
          localStorage.setItem('activeTab', $(e.target).attr('href'));
      });
      var activeTab = localStorage.getItem('activeTab');
      if(activeTab){
          $('#tabMenu a[href="' + activeTab + '"]').tab('show');
      }
    });

    $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
      $("#success-alert").slideUp(500);
    });

    $('#update-email').on('click', function(){
      $('#modal-judul').html("Update email");
      $('#tombol-simpan').val("update-email");
      $('#update-email-modal').modal('show');
    })

</script>

@endsection