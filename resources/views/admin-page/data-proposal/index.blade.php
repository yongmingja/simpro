@extends('layouts.backend')
@section('title','Data Proposal')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('dashboard-admin')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('data-proposal.index')}}">@yield('title')</a>
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
            <div class="col-12">
                <div class="card table-responsive">
                    <div class="card-body">
                            <table class="table table-hover table-responsive" id="table_proposal">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Jenis Proposal</th>
                                  <th>Nama Kegiatan</th>
                                  <th>Nama Fakultas</th>
                                  <th>Nama Prodi</th>
                                  <th>Actions</th>
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
                                    <h5 class="modal-title justify-content-center">Validasi Sarana Prasarana</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table" class="col-sm-12 table-responsive mb-3"></div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of validasi proposal-->

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
                                                <input type="hidden" class="form-control" id="sarpras_id" name="sarpras_id">
                                                <label for="alasan" class="form-label">Keterangan ditolak</label>
                                                <textarea class="form-control" id="alasan" name="alasan" rows="5"></textarea>
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
        var table = $('#table_proposal').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('data-proposal.index') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'nama_jenis_kegiatan',name: 'nama_jenis_kegiatan',
                    render: function (data, type, row, meta){
                        return row.nama_jenis_kegiatan +' '+ row.preview
                    }
                },
                {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                {data: 'nama_fakultas',name: 'nama_fakultas'},
                {data: 'nama_prodi',name: 'nama_prodi'},
                {data: 'action',name: 'action'},
            ]
        });
    });

    $(document).on('click','.validasi-proposal', function(){
        dataId = $(this).data('id');
        $.ajax({
            url: "{{route('validasi-proposal')}}",
            method: "GET",
            data: {proposal_id: dataId},
            success: function(response, data){
                $('#validasi-prop').modal('show');
                $("#table").html(response.card)
            }
        })
    });

    $('body').on('click','.tombol-yes', function(){
        var data_id = $(this).attr('data-id');
        $.ajax({
            url: "{{route('valid-y')}}",
            type: "POST",
            data: {
                sarpras_id: data_id,
                _token: '{{csrf_token()}}'
            },
            dataType: 'json',
            success: function (data) {
                Swal.fire({
                    title: 'Agree!',
                    text: 'Data saved successfully!',
                    type: 'success',
                    customClass: {
                    confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false,
                    timer: 2000
                })
                location.reload();
            }
        });
    });

    $('body').on('click','.tombol-no', function(){
        var data_id = $(this).attr('data-id');
        $('#form-add-keterangan').trigger("reset");
        $('#modal-judul').html("Tambah keterangan");
        $('#add-keterangan-modal').modal('show');
        $('#sarpras_id').val(data_id);
    });
    if ($("#form-add-keterangan").length > 0) {
        $("#form-add-keterangan").validate({
            submitHandler: function (form) {
                var actionType = $('#tombol-simpan').val();
                $('#tombol-simpan').html('Saving..');

                $.ajax({
                    data: $('#form-add-keterangan').serialize(), 
                    url: "{{route('valid-n')}}",
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
                        location.reload();
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

</script>

@endsection