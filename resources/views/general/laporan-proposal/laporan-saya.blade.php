@extends('layouts.backend')
@section('title','Laporan Proposal')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="javascript:void(0)">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('my-report')}}">@yield('title')</a>
      </li>
      <li class="breadcrumb-item active">Data</li>
    </ol>
</nav>
</div>
@endsection
<style>
    .horizontal-timeline .items {
    border-top: 3px solid #e9ecef;
    }

    .horizontal-timeline .items .items-list {
    display: block;
    position: relative;
    text-align: center;
    padding-top: 70px;
    margin-right: 0;
    }

    .horizontal-timeline .items .items-list:before {
    content: "";
    position: absolute;
    height: 36px;
    border-right: 2px dashed #dee2e6;
    top: 0;
    }

    .horizontal-timeline .items .items-list .event-date {
    position: absolute;
    top: 36px;
    left: 0;
    right: 0;
    width: 90px;
    margin: 0 auto;
    font-size: 0.7rem;
    padding-top: 8px;
    }

    @media (min-width: 1140px) {
    .horizontal-timeline .items .items-list {
        display: inline-block;
        width: 24%;
        padding-top: 45px;
    }

    .horizontal-timeline .items .items-list .event-date {
        top: -40px;
    }
    }
</style>

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
                        <table class="table table-hover table-responsive" id="table_proposal">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Laporan</th>
                                <th>Kategori</th>
                                <th>Nama Kegiatan</th>
                                <th>Tgl dibuat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                        </table>
                    </div>                    
                </div>

                <!-- Mulai modal informasi -->
                <div class="modal fade" tabindex="-1" role="dialog" id="show-informasi" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title justify-content-center">Informasi Laporan Proposal</h5>                                    
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="table_show_informasi" class="col-sm-12 table-responsive mb-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of modal informasi-->
                <!-- modal edit hasil kegiatan-->
                <div class="modal fade mt-3" tabindex="-1" role="dialog" id="edithasilkegiatan-modal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-judul-edit-hasil-kegiatan">Edit Nama Kegiatan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="form-edit-hasil-kegiatan" name="form-edit-hasil-kegiatan" class="form-horizontal">
                                    <input type="hidden" id="props_id_hasil_kegiatan" name="props_id_hasil_kegiatan" class="form-control">  
                                    <div class="mb-2">
                                        <label for="e_hasil_kegiatan" class="form-label">Hasil Kegiatan</label>
                                        <div id="editor-hasil-kegiatan" class="mb-3" style="height: 300px;"></div>
                                        <textarea rows="3" class="mb-3 d-none" name="e_hasil_kegiatan" id="e_hasil_kegiatan"></textarea>
                                    </div>  
                                    <div class="col-sm-offset-2 col-sm-12">
                                        <hr class="mt-2">
                                        <div class="float-sm-end">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary btn-block" id="btn-update-hasil-kegiatan" value="create">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of modal edit hasil kegiatan-->
                <!-- modal edit hasil kegiatan-->
                <div class="modal fade mt-3" tabindex="-1" role="dialog" id="editcatatan-modal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-judul-edit-catatan">Edit Evaluasi Catatan Kegiatan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="form-edit-catatan" name="form-edit-catatan" class="form-horizontal">
                                    <input type="hidden" id="props_id_catatan_kegiatan" name="props_id_catatan_kegiatan" class="form-control">  
                                    <div class="mb-2">
                                        <label for="e_catatan_kegiatan" class="form-label">Evaluasi Catatan Kegiatan</label>
                                        <div id="editor-catatan" class="mb-3" style="height: 300px;"></div>
                                        <textarea rows="3" class="mb-3 d-none" name="e_catatan_kegiatan" id="e_catatan_kegiatan"></textarea>
                                    </div>  
                                    <div class="col-sm-offset-2 col-sm-12">
                                        <hr class="mt-2">
                                        <div class="float-sm-end">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary btn-block" id="btn-update-catatan-kegiatan" value="create">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of modal edit hasil kegiatan-->

                <!-- modal edit penutup -->
                <div class="modal fade mt-3" tabindex="-1" role="dialog" id="editpenutup-modal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-judul-edit-penutup">Edit Penutup</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="form-edit-penutup" name="form-edit-penutup" class="form-horizontal">
                                    <input type="hidden" id="props_id_penutup" name="props_id_penutup" class="form-control">  
                                    <div class="mb-2">
                                        <label for="e_detil_kegiatan" class="form-label">Penutup</label>
                                        <div id="editor-penutup" class="mb-3" style="height: 300px;"></div>
                                        <textarea rows="3" class="mb-3 d-none" name="e_penutup" id="e_penutup"></textarea>
                                    </div>  
                                    <div class="col-sm-offset-2 col-sm-12">
                                        <hr class="mt-2">
                                        <div class="float-sm-end">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary btn-block" id="btn-update-penutup" value="create">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of modal edit penutup-->

                <!-- Mulai modal informasi -->
                <div class="modal fade" tabindex="-1" role="dialog" id="show-done-revision" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title justify-content-center">Konfirmasi Selesai Revisi</h5>                                    
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="table_show_done_revision" class="col-sm-12 table-responsive mb-3"></div>
                                <input type="hidden" id="edit_id" name="edit_id">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <input type="checkbox" id="check_finish" class="form-check-input"><small>&nbsp;&nbsp;Selesai Revisi</small>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-primary btn-sm" id="submit-ulang">Ajukan kembali</button>
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>                                        
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of modal informasi-->
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
        var table = $('#table_proposal').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('my-report') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'laporan',name: 'laporan'},
                {data: 'nama_jenis_kegiatan',name: 'nama_jenis_kegiatan'},
                {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                {data: 'tgl_proposal',name: 'tgl_proposal',
                    render: function ( data, type, row ){
                        if(row.tgl_proposal == null){
                            return '-';
                        } else {
                            return moment(row.tgl_proposal).format("DD MMM YYYY")
                        }
                    }
                },
                {data: 'status',name: 'status'},
                {data: 'action',name: 'action'},
            ]
        });
    });

    $(document).on('click','.lihat-proposal', function(){
        dataId = $(this).data('id');
        $.ajax({
            url: "{{route('check-status-proposal')}}",
            method: "GET",
            data: {proposal_id: dataId},
            success: function(response, data){
                $('#show-detail').modal('show');
                $("#table").html(response.card)
            }
        })
    });

    // TOMBOL DELETE
    $(document).on('click', '.delete', function () {
        dataId = $(this).attr('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "It will be deleted permanently!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            showLoaderOnConfirm: true,
            preConfirm: function() {
                return new Promise(function(resolve) {
                    $.ajax({
                        url: "{{route('delete-my-report')}}",
                        type: 'DELETE',
                        data: {id:dataId},
                        dataType: 'json'
                    }).done(function(response) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Your data has been deleted.',
                            type: 'success',
                            timer: 2000
                        })
                        $('#table_proposal').DataTable().ajax.reload(null, true);
                    }).fail(function() {
                        Swal.fire({
                            title: 'Oops!',
                            text: 'Something went wrong with ajax!',
                            type: 'error',
                            timer: 2000
                        })
                    });
                });
            },
        });
    });

    $('body').on('click','.info-ditolakdekan',function(){
    var dataKet = $(this).attr('data-keteranganditolak');
    alert(dataKet);
    });

    // Page untuk revisi laporan proposal
    $(document).on('click','.revisi-informasi', function(){
        dataId = $(this).data('id');
        $.ajax({
            url: "{{route('check-informasi-lap-proposal')}}",
            method: "GET",
            data: {proposal_id: dataId},
            success: function(response, data){
                $('#show-informasi').modal('show');
                $("#table_show_informasi").html(response.card)
            }
        })
    });
    $('body').on('click','.edit-hasil-kegiatan', function(){
        var idProps = $(this).attr('data-id-proposal');
        var dataHasilKegiatan = $(this).attr('data-hasil-kegiatan');
        $('#modal-judul-edit-hasil-kegiatan').html("Edit Hasil Kegiatan");
        $('#edithasilkegiatan-modal').modal('show');
        $('#props_id_hasil_kegiatan').val(idProps);
        $('#e_hasil_kegiatan').val(dataHasilKegiatan);
    });
    $(document).on('click','#btn-update-hasil-kegiatan', function(){
        var actionType = $('#btn-update-hasil-kegiatan').val();
        $('#btn-update-hasil-kegiatan').html('Updating..');
        $.ajax({
            data: $('#form-edit-hasil-kegiatan').serialize(),
            url: "{{ route('update-hasil-kegiatan') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('#form-edit-hasil-kegiatan').trigger("reset");
                $('#edithasilkegiatan-modal').modal('hide');
                $('#btn-update-hasil-kegiatan').html('Update');
                $('#table_proposal').DataTable().ajax.reload(null, true);
                Swal.fire({
                    title: 'Good job!',
                    text: 'Data updated successfully!',
                    type: 'success',
                    customClass: {
                    confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false,
                    timer: 2000
                })
            },
            error: function(response) {
                $('#btn-update-hasil-kegiatan').html('Update');
                Swal.fire({
                    title: 'Error!',
                    text: 'Data failed to update!',
                    type: 'error',
                    customClass: {
                    confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false,
                    timer: 2000
                })
            }
        });
    })

    $('body').on('click','.edit-catatan', function(){
        var idProps = $(this).attr('data-id-proposal');
        var dataCatatan = $(this).attr('data-catatan');
        $('#modal-judul-edit-catatan').html("Edit Evaluasi Catatan Kegiatan");
        $('#editcatatan-modal').modal('show');
        $('#props_id_catatan_kegiatan').val(idProps);
        $('#e_catatan_kegiatan').val(dataCatatan);
    });
    $(document).on('click','#btn-update-catatan-kegiatan', function(){
        var actionType = $('#btn-update-catatan-kegiatan').val();
        $('#btn-update-catatan-kegiatan').html('Updating..');
        $.ajax({
            data: $('#form-edit-catatan').serialize(),
            url: "{{ route('update-catatan-kegiatan') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('#form-edit-catatan').trigger("reset");
                $('#editcatatan-modal').modal('hide');
                $('#btn-update-catatan-kegiatan').html('Update');
                $('#table_proposal').DataTable().ajax.reload(null, true);
                Swal.fire({
                    title: 'Good job!',
                    text: 'Data updated successfully!',
                    type: 'success',
                    customClass: {
                    confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false,
                    timer: 2000
                })
            },
            error: function(response) {
                $('#btn-update-catatan-kegiatan').html('Update');
                Swal.fire({
                    title: 'Error!',
                    text: 'Data failed to update!',
                    type: 'error',
                    customClass: {
                    confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false,
                    timer: 2000
                })
            }
        });
    })

    $('body').on('click','.edit-penutup', function(){
        var idProps = $(this).attr('data-id-proposal');
        var dataPenutup = $(this).attr('data-penutup');
        $('#modal-judul-edit-penutup').html("Edit Penutup");
        $('#editpenutup-modal').modal('show');
        $('#props_id_penutup').val(idProps);
        $('#e_penutup').val(dataPenutup);
    });
    $(document).on('click','#btn-update-penutup', function(){
        var actionType = $('#btn-update-penutup').val();
        $('#btn-update-penutup').html('Updating..');
        $.ajax({
            data: $('#form-edit-penutup').serialize(),
            url: "{{ route('update-penutup-laporan-proposal') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('#form-edit-penutup').trigger("reset");
                $('#editpenutup-modal').modal('hide');
                $('#btn-update-penutup').html('Update');
                $('#table_proposal').DataTable().ajax.reload(null, true);
                Swal.fire({
                    title: 'Good job!',
                    text: 'Data updated successfully!',
                    type: 'success',
                    customClass: {
                    confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false,
                    timer: 2000
                })
            },
            error: function(response) {
                $('#btn-update-penutup').html('Update');
                Swal.fire({
                    title: 'Error!',
                    text: 'Data failed to update!',
                    type: 'error',
                    customClass: {
                    confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false,
                    timer: 2000
                })
            }
        });
    })

    document.addEventListener('DOMContentLoaded', function() {
          if (document.getElementById('e_hasil_kegiatan')) {
              var editor = new Quill('#editor-hasil-kegiatan', {
                  theme: 'snow'
              });
              var quillEditor = document.getElementById('e_hasil_kegiatan');
              editor.on('text-change', function() {
                  quillEditor.value = editor.root.innerHTML;
              });

              quillEditor.addEventListener('input', function() {
                  editor.root.innerHTML = quillEditor.value;
              });
          }

          if (document.getElementById('e_catatan_kegiatan')) {
              var editor1 = new Quill('#editor-catatan', {
                  theme: 'snow'
              });
              var quillEditor1 = document.getElementById('e_catatan_kegiatan');
              editor1.on('text-change', function() {
                  quillEditor1.value = editor1.root.innerHTML;
              });

              quillEditor1.addEventListener('input', function() {
                  editor1.root.innerHTML = quillEditor1.value;
              });
          }  
          
          if (document.getElementById('e_penutup')) {
              var editor2 = new Quill('#editor-penutup', {
                  theme: 'snow'
              });
              var quillEditor2 = document.getElementById('e_penutup');
              editor2.on('text-change', function() {
                  quillEditor2.value = editor2.root.innerHTML;
              });

              quillEditor2.addEventListener('input', function() {
                  editor2.root.innerHTML = quillEditor2.value;
              });
          }
    });

    $(document).on('click','.done-revision', function(){
        dataId = $(this).data('id');
        val_idProp = $('#edit_id').val(dataId);
        $.ajax({
            url: "{{route('check-done-revision')}}",
            method: "GET",
            data: {proposal_id: dataId},
            success: function(response, data){
                $('#show-done-revision').modal('show');
                $("#table_show_done_revision").html(response.card)
            }
        })
    });

    $('#submit-ulang').prop('disabled',true);
    $('#check_finish').on('change', function() {
        if($("#check_finish").prop('checked')){
            $('#submit-ulang').prop('disabled',false);
        }else{
            $('#submit-ulang').prop('disabled',true);
        }
    });

    $('#submit-ulang').click(function () {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Silakan periksa kembali semua inputan anda sebelum konfirmasi untuk submit ulang!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sure, confirm',
            preConfirm: () => {
                return $.ajax({
                    url: "{{route('confirm-done-revision')}}",
                    type: 'POST',
                    data: { id_proposal: $('#edit_id').val() },
                    dataType: 'json',
                }).done(() => {
                    Swal.fire({
                        title: 'Saved!',
                        text: 'Your data has been saved.',
                        icon: 'success',
                        timer: 2000,
                    });
                    location.reload();
                }).fail(() => {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Something went wrong with ajax!',
                        icon: 'error',
                        timer: 2000,
                    });
                });
            },
        });
    });


</script>

@endsection