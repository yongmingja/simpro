@extends('layouts.backend')
@section('title','Undangan')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('rundanganfpku')}}">@yield('title')</a>
      </li>
      <li class="breadcrumb-item active">Data</li>
    </ol>
</nav>
</div>
@endsection
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/typography.css')}}" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />
@section('content')

<div class="container-fluid flex-grow-1">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card table-responsive">
                    <div class="card-body">
                        <div class="col-sm-2 mb-3">
                            <fieldset class="form-group">
                                <select style="cursor:pointer;" class="select2 form-control" id="status" name="status" required>
                                    <option value="all" selected>Semua data (default)</option>
                                    <option value="pending">Pending</option>
                                    <option value="accepted">Diterima</option>
                                </select>
                            </fieldset>
                        </div>
                        <table class="table table-hover table-responsive" id="table_fpku">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Validasi</th>
                                <th>Undangan</th>
                                <th>Nama Kegiatan</th>
                                <th>Tgl Kegiatan</th>
                                <th>Ketua Pelaksana</th>
                                <th>Peserta</th>
                                <th>Lampiran</th>
                                <th>History Delegasi</th>
                            </tr>
                            </thead>
                        </table>
                    </div>                    
                </div>

                <!-- Mulai modal lihat lampiran -->
                <div class="modal fade" tabindex="-1" role="dialog" id="show-lampiran" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title justify-content-center">Lampiran FPKU</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="table_lampiran" class="col-sm-12 table-responsive mb-3"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of modal lihat lampiran-->

                <!-- Modal validasi undangan -->
                <div class="modal fade" id="add-delegasi-modal" aria-hidden="true">
                    <div class="modal-dialog ">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-judul"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="form-add-delegasi" name="form-add-delegasi" class="form-horizontal">
                                    <div class="row">
                                        <div class="mb-3">
                                            <input type="hidden" class="form-control" id="fpku_id" name="fpku_id">
                                            <label for="catatan_delegator" class="form-label">Catatan untuk delegasi</label>
                                            <div id="editor-delegasi" class="mb-3" style="height: 300px;"></div>
                                            <textarea rows="3" class="mb-3 d-none" id="catatan_delegator" name="catatan_delegator" placeholder="Silahkan tulis secara rinci dan jelas"></textarea>
                                            <span class="text-danger" id="catatanDelegatorErrorMsg" style="font-size: 10px;"></span>
                                        </div>  
                                        <div class="mb-3">
                                            <label for="delegasi" class="form-label">Delegasi (biro terkait)</label>
                                            <select class="form-select select2" multiple id="delegasi" name="delegasis[]" aria-label="Default select example" style="cursor:pointer;">
                                                <option value="" id="pilih_pegawai">- Pilih -</option>
                                                @foreach($getDataPegawai as $pegawai)
                                                <option value="{{$pegawai->id}}">{{$pegawai->nama_pegawai}}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger" id="delegasiErrorMsg" style="font-size: 10px;"></span>
                                        </div>                                        
                                        
                                        <div class="col-sm-offset-2 col-sm-12">
                                            <hr class="mt-2">
                                            <div class="float-sm-end">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-block" id="tombol-simpan" value="create"><i class="bx bx-paper-plane"></i> Kirim</button>
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
                <!-- End of validasi undangan-->

                <!-- Mulai modal show history delegasi  -->
                <div class="modal fade" tabindex="-1" role="dialog" id="show-history" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title justify-content-center">History Delegasi Data ini</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="table_show_history" class="col-sm-12 table-responsive mb-3"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of modal show history delegasi -->
            </div>
        </div>
    </section>
</div>

@endsection
@section('script')
<script>
    const quill = new Quill('#editor', {
        theme: 'snow'
    });
</script>

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
        function fill_datatable(status = ''){
            var table = $('#table_fpku').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rundanganfpku') }}",
                    "type": "GET",
                    "data": function(data){
                        data.status = $('#status').val();
                    }
                },
                columns: [
                    {data: null,sortable:false,
                        render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, 
                    {data: 'action',name: 'action'},
                    {data: 'undangan',name: 'undangan'},
                    {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                    {data: 'tgl_kegiatan',name: 'tgl_kegiatan',
                        render: function ( data, type, row ){
                            return moment(row.tgl_kegiatan).format("DD MMM YYYY")
                        }
                    },
                    {data: 'ketua_pelaksana',name: 'ketua_pelaksana'},
                    {data: 'nama_pegawai',name: 'nama_pegawai'},
                    {data: 'lampirans',name: 'lampirans'},
                    {data: 'lihatDelegasi',name: 'lihatDelegasi'},
                ]
            });
        }
        $('#status').on('change', function(e){
            var status = this.value;

            if(status != ''){
                $('#table_fpku').DataTable().destroy();
                fill_datatable(status);
            } else {
                alert('Anda belum memilih filter.');
                $('#table_fpku').DataTable().destroy();
                fill_datatable();
            }
        });
    });

    $('body').on('click','.tombol-yes', function(){
        var idFpku = $(this).data('id');
        $('#form-add-delegasi').trigger("reset");
        $('#modal-judul').html("Tambah Delegasi");
        $('#add-delegasi-modal').modal('show');
        $('#fpku_id').val(idFpku);
    });
    if ($("#form-add-delegasi").length > 0) {
        $("#form-add-delegasi").validate({
            submitHandler: function (form) {
                var actionType = $('#tombol-simpan').val();
                $('#tombol-simpan').html('');
                $('#tombol-simpan').prop("disabled", true);

                $.ajax({
                    data: $('#form-add-delegasi').serialize(), 
                    url: "{{route('confirmundanganfpku')}}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function(){
                                $("#tombol-simpan").append(
                                    '<i class="bx bx-loader-circle bx-spin text-warning"></i>'+
                                    ' Mohon tunggu ...');
                            },
                    success: function (data) {
                        $('#form-add-delegasi').trigger("reset");
                        $('#add-delegasi-modal').modal('hide');
                        $('#tombol-simpan').html('Kirim');
                        $('#tombol-simpan').prop("disabled", true);
                        $('#table_fpku').DataTable().ajax.reload(null, true);
                        Swal.fire({
                            title: 'Good job!',
                            text: 'Data sent successfully!',
                            type: 'success',
                            customClass: {
                            confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false,
                            timer: 2000
                        })
                    },
                    error: function(response) {
                        $('#tombol-simpan').html('Kirim');
                        $('#tombol-simpan').prop("disabled", false);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Data failed to send!',
                            type: 'error',
                            customClass: {
                            confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false,
                            timer: 2000
                        })
                    }
                });
            }
        })
    }

    $('body').on('click','.lihat-lampiran', function(){
        var data_id = $(this).data('id');
        $.ajax({
            url: "{{route('view-lampiran-data-fpku')}}",
            method: "GET",
            data: {
                fpku_id: data_id,  
                "_token": "{{ csrf_token() }}",
            },
            success: function(response, data){
                $('#show-lampiran').modal('show');
                $("#table_lampiran").html(response.card)
            }
        })
    });

    document.addEventListener('DOMContentLoaded', function() {
          if (document.getElementById('catatan_delegator')) {
              var editor = new Quill('#editor-delegasi', {
                  theme: 'snow'
              });
              var quillEditor = document.getElementById('catatan_delegator');
              editor.on('text-change', function() {
                  quillEditor.value = editor.root.innerHTML;
              });

              quillEditor.addEventListener('input', function() {
                  editor.root.innerHTML = quillEditor.value;
              });
          }
    });

    $('body').on('click','.lihat-delegasi', function(){
        var idFpku = $(this).data('id');
        $.ajax({
            url: "{{route('lihat-history-delegasi')}}",
            method: "GET",
            data: {fpku_id: idFpku},
            success: function(response, data){
                $('#show-history').modal('show');
                $("#table_show_history").html(response.card)
            }
        })
    });

</script>

@endsection