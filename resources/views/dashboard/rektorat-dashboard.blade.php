@extends('layouts.backend')
@section('title','Dashboard')
@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('dashboard-rektorat')}}">@yield('title')</a>
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
    <!-- Content -->
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="row">

            <div class="col-12">
                <div class="card table-responsive">
                    <div class="card-body">
                        <div class="col-sm-2 mb-3">
                            <fieldset class="form-group">
                                <select style="cursor:pointer;" class="select2 form-control" id="status" name="status" required>
                                    <option value="all" selected>Semua Proposal (default)</option>
                                    <option value="pending">Pending</option>
                                    <option value="accepted">Diterima</option>
                                    <option value="denied">Ditolak</option>
                                </select>
                            </fieldset>
                        </div>
                            <table class="table table-hover table-responsive" id="table_proposal">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Nama Kegiatan</th>
                                  <th>Tgl Kegiatan</th>
                                  <th>Proposal Dibuat</th>
                                  <th>Detail Anggaran</th>
                                  <th>Fakultas / Biro</th>
                                  <th>Prodi / Biro</th>
                                  <th width="12%;">Status</th>
                                  <th>Lampiran</th>
                                  <th>History Delegasi</th>
                                </tr>
                              </thead>
                            </table>
                        </div>
                    </div>

                    <!-- Modal validasi proposal -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="validasi-prop" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">Validasi Rencana Anggaran</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table" class="col-sm-12 table-responsive mb-3"></div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of validasi proposal-->

                    <!-- Modal validasi proposal -->
                    <div class="modal fade" id="add-keterangan-modal" aria-hidden="true">
                        <div class="modal-dialog ">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-add-keterangan" name="form-add-keterangan" class="form-horizontal">
                                        <div class="row">
                                            <div class="mb-3">
                                                <input type="hidden" class="form-control" id="propsl_id" name="propsl_id">
                                                <label for="keterangan_ditolak" class="form-label">Keterangan ditolak</label>
                                                <textarea class="form-control" id="keterangan_ditolak" name="keterangan_ditolak" rows="5"></textarea>
                                                <span class="text-danger" id="alasanErrorMsg" style="font-size: 10px;"></span>
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
                    <!-- End of validasi proposal-->

                    <!-- Mulai modal detail -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="show-detail" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">Lampiran Proposal</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table_l" class="col-sm-12 table-responsive mb-3"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of modal detail-->

                    <!-- Modal validasi proposal -->
                <div class="modal fade" id="add-delegasi-modal" aria-hidden="true">
                    <div class="modal-dialog ">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-judul-delegasi"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="form-add-delegasi" name="form-add-delegasi" class="form-horizontal">
                                    <div class="row">
                                        <div class="mb-3">
                                            <input type="hidden" class="form-control" id="proposal_id" name="proposal_id">
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
                                                <button type="submit" class="btn btn-primary btn-block" id="tombol-kirim" value="create"><i class="bx bx-paper-plane"></i> Kirim</button>
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
                <!-- End of validasi proposal-->

                <!-- Mulai modal lihat detail anggaran -->
                <div class="modal fade mt-3" tabindex="-1" role="dialog" id="show-detail-anggaran" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title justify-content-center">Detail Anggaran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="table_detail_anggaran" class="col-sm-12 table-responsive mb-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of modal lihat detail anggaran-->

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
        </div>
    
    </div>
    <!-- / Content -->

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
        function fill_datatable(status = '') {
            var table = $('#table_proposal').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dashboard-rektorat') }}",
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
                    {data: 'tgl_event',name: 'tgl_event',
                        render: function ( data, type, row ){
                            return moment(row.tgl_event).format("DD MMM YYYY")
                        }
                    },
                    {data: 'created_at',name: 'created_at',
                        render: function ( data, type, row ){
                            return moment(row.created_at).format("DD MMM YYYY")
                        }
                    },
                    {data: 'detail',name: 'detail'},
                    {data: 'nama_fakultas_biro',name: 'nama_fakultas_biro'},
                    {data: 'nama_prodi_biro',name: 'nama_prodi_biro'},
                    {data: 'validasi',name: 'validasi'},
                    {data: 'vlampiran',name: 'vlampiran'},
                    {data: 'lihatDelegasi',name: 'lihatDelegasi'},
                ]
            });
        }
        $('#status').on('change', function(e){
            var status = this.value;

            if(status != ''){
                $('#table_proposal').DataTable().destroy();
                fill_datatable(status);
            } else {
                alert('Anda belum memilih filter.');
                $('#table_proposal').DataTable().destroy();
                fill_datatable();
            }
        });
    });

    $('body').on('click','.tombol-yes', function(){
        var data_id = $(this).attr('data-id');  
        $('#form-add-delegasi').trigger("reset");
        $('#modal-judul-delegasi').html("Tambah Delegasi");
        $('#add-delegasi-modal').modal('show');
        $('#proposal_id').val(data_id);
    });
    if ($("#form-add-delegasi").length > 0) {
        $("#form-add-delegasi").validate({
            submitHandler: function (form) {
                var actionType = $('#tombol-kirim').val();
                $('#tombol-kirim').html('');
                $('#tombol-kirim').prop("disabled", true);

                $.ajax({
                    data: $('#form-add-delegasi').serialize(), 
                    url: "{{route('approval-y')}}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function(){
                                $("#tombol-kirim").append(
                                    '<i class="bx bx-loader-circle bx-spin text-warning"></i>'+
                                    ' Mohon tunggu ...');
                            },
                    success: function (data) {
                        $('#form-add-delegasi').trigger("reset");
                        $('#add-delegasi-modal').modal('hide');
                        $('#tombol-kirim').html('Kirim');
                        $('#tombol-kirim').prop("disabled", true);
                        $('#table_proposal').DataTable().ajax.reload(null, true);
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
                        $('#tombol-kirim').html('Kirim');
                        $('#tombol-kirim').prop("disabled", false);
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

    $('body').on('click','.tombol-no', function(){
        var data_id = $(this).attr('data-id');
        $('#form-add-keterangan').trigger("reset");
        $('#modal-judul').html("Tambah keterangan");
        $('#add-keterangan-modal').modal('show');
        $('#propsl_id').val(data_id);
    });
    if ($("#form-add-keterangan").length > 0) {
        $("#form-add-keterangan").validate({
            submitHandler: function (form) {
                var actionType = $('#tombol-simpan').val();
                $('#tombol-simpan').html('Saving..');

                $.ajax({
                    data: $('#form-add-keterangan').serialize(), 
                    url: "{{route('approval-n')}}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-add-keterangan').trigger("reset");
                        $('#add-keterangan-modal').modal('hide');
                        $('#tombol-simpan').html('Save');
                        $('#table_proposal').DataTable().ajax.reload(null, true);
                        Swal.fire({
                            title: 'Good job!',
                            text: 'Data saved successfully!',
                            type: 'success',
                            customClass: {
                            confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false,
                            timer: 2000
                        })
                    },
                    error: function(response) {
                        $('#tombol-simpan').html('Save');
                        Swal.fire({
                            title: 'Error!',
                            text: 'Data failed to save!',
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

    $('body').on('click','.v-lampiran', function(){
        var data_id = $(this).data('id');
        $.ajax({
            url: "{{route('view-lampiran-proposal')}}",
            method: "GET",
            data: {proposal_id: data_id},
            success: function(response, data){
                $('#show-detail').modal('show');
                $("#table_l").html(response.card)
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

    $('body').on('click','.lihat-detail', function(){
        var data_id = $(this).data('id');
        $.ajax({
            url: "{{route('lihat-detail-anggaran')}}",
            method: "GET",
            data: {
                proposal_id: data_id,  
                "_token": "{{ csrf_token() }}",
            },
            success: function(response, data){
                $('#show-detail-anggaran').modal('show');
                $("#table_detail_anggaran").html(response.card)
            }
        })
    });

    $('body').on('click','.lihat-delegasi', function(){
        var id_proposal = $(this).data('id');
        $.ajax({
            url: "{{route('lihat-history-delegasi-proposal')}}",
            method: "GET",
            data: {proposal_id: id_proposal},
            success: function(response, data){
                $('#show-history').modal('show');
                $("#table_show_history").html(response.card)
            }
        })
    });
</script>
@endsection