@extends('layouts.backend')
@section('title','Data FPKU')

@section('breadcrumbs')
<div class="container-fluid">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{route('home')}}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('data-fpku.index')}}">@yield('title')</a>
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
                        <!-- MULAI TOMBOL TAMBAH -->
                        <div class="mb-3">
                            <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body" id="tombol-tambah"><button type="button" class="btn btn-primary"><i class="bx bx-plus-circle bx-spin-hover"></i> New data</button></a>
                        </div>
                        
                        <!-- AKHIR TOMBOL -->
                            <table class="table table-hover table-responsive" id="table_fpku">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Nama Kegiatan</th>
                                  <th>Tgl Kegiatan</th>
                                  <th>Peserta Kegiatan</th>
                                  <th>Aksi</th>
                                </tr>
                              </thead>
                            </table>
                        </div>
                    </div>

                    <!-- MULAI MODAL FORM TAMBAH/EDIT-->
                    <div class="modal fade" id="tambah-edit-modal" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-tambah-edit" name="form-tambah-edit" class="form-horizontal">
                                        <div class="row p-3">
                                            <input type="hidden" id="id" name="id">
                                            <div style="text-align: center;">
                                                <label for="cek_tanggal" class="form-label mb-2">Apakah perlu pengecekan tanggal kegiatan ?</label>
                                                <div class="mb-2">
                                                    <label class="switch switch-warning">
                                                        <span class="switch-label">Tidak</span>
                                                        <input type="checkbox" class="switch-input" value="1" id="cek_tanggal" name="cek_tanggal" />
                                                        <span class="switch-toggle-slider">
                                                        <span class="switch-on">
                                                            <i class="bx bx-check"></i>
                                                        </span>
                                                        <span class="switch-off">
                                                            <i class="bx bx-x"></i>
                                                        </span>
                                                        </span>
                                                        <span class="switch-label">Ya</span>
                                                    </label>
                                                </div>
                                            </div> 
                                            <div class="mb-3 mt-3">
                                                <label for="no_surat_undangan" class="form-label">No Surat Undangan</label>
                                                <input type="text" class="form-control" id="no_surat_undangan" name="no_surat_undangan" placeholder="contoh: No. 001/2024" value="" autofocus />
                                                <span class="text-danger" id="noUndanganErrorMsg" style="font-size: 10px;"></span>
                                            </div>                                          
                                            <div class="mb-3">
                                                <label for="undangan_dari" class="form-label">Undangan Dari</label>
                                                <input type="text" class="form-control" id="undangan_dari" name="undangan_dari" value="" />
                                                <span class="text-danger" id="undanganDariErrorMsg" style="font-size: 10px;"></span>
                                            </div>                                          
                                            <div class="mb-3">
                                                <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                                                <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan" value="" />
                                                <span class="text-danger" id="namaKegiatanErrorMsg" style="font-size: 10px;"></span>
                                            </div>  
                                            <div class="mb-3">
                                                <label for="tgl_kegiatan" class="form-label">Tanggal Kegiatan</label>
                                                <input type="date" class="form-control" id="tgl_kegiatan" name="tgl_kegiatan" value="" placeholder="mm/dd/yyyy" />
                                                {{-- <div class="d-none text-warning mt-1" id="teks_warning" style="font-size: 10px;">Tanggal harus lebih dari sama dengan 14 hari</div> --}}
                                                <span class="text-danger" id="tglKegiatanErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            <div class="mb-3">
                                                <label for="ketua" class="form-label">PIC Pembuat Laporan</label>
                                                <select class="form-select select2" id="ketua" name="ketua" aria-label="Default select example" style="cursor:pointer;">
                                                    <option value="" id="pilih_ketua">- Pilih -</option>
                                                    @foreach($getDataPegawai as $pegawai)
                                                    <option value="{{$pegawai->id}}">{{$pegawai->nama_pegawai}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="ketuaErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            <div class="mb-3">
                                                <label for="id_pegawai" class="form-label">Peserta Kegiatan (Include PIC Pembuat Laporan)</label>
                                                <select class="form-select select2" multiple id="id_pegawai" name="id_pegawais[]" aria-label="Default select example" style="cursor:pointer;">
                                                    <option value="" id="pilih_pegawai">- Pilih -</option>
                                                    @foreach($getDataPegawai as $pegawai)
                                                    <option value="{{$pegawai->id}}">{{$pegawai->nama_pegawai}}</option>
                                                    @endforeach
                                                </select>
                                                <div class="mt-2 text-info" style="font-size: 11px;">**Anda bisa memilih lebih dari satu peserta</div>
                                                <span class="text-danger" id="idPegawaiErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            
                                            <div class="mb-3">
                                                <label for="keperluan" class="form-label">Keperluan yang perlu dipersiapkan:</label>
                                                <div class="col-sm-0">
                                                    <table class="table table-borderless" id="dynamicAdd">
                                                        <tr>
                                                            <td><button type="button" class="btn btn-warning btn-block mt-2" id="tombol-add-keperluan"><i class="bx bx-plus-circle"></i></button></td>
                                                            <td><input type="text" class="form-control" id="isi_keperluan" name="kolom[0][isi_keperluan]" placeholder="(isi keperluan 1)" value="" /></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div> 

                                            <div class="mb-3">
                                                <label for="catatan" class="form-label">Catatan</label>
                                                <textarea type="text" class="form-control" id="catatan" name="catatan" placeholder="(Opsional atau boleh dikosongkan)" rows="3"/></textarea>
                                                <span class="text-danger" id="catatanErrorMsg" style="font-size: 10px;"></span>
                                            </div> 
                                            <div class="mb-3">
                                                <label for="lampiran" class="form-label">Lampiran FPKU <i>(Opsional)</i></label>
                                                <div class="col-sm-0 firstRow">
                                                    <table class="table table-borderless">
                                                        <tr>
                                                            <td><button type="button" class="btn btn-warning btn-block addField mt-2"><i class="bx bx-plus-circle bx-xs"></i></button></td>
                                                            <td><input type="text" name="nama_berkas[]" class="w-100 form-control" placeholder="input nama berkas"></td>
                                                            <td><input type="file" name="berkas[]" class="w-100 form-control" accept=".pdf, .docs, .docx"></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-offset-2 col-sm-12">
                                                <hr class="mt-2">
                                                <div class="float-sm-end">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary btn-block" id="tombol-simpan" value="create">Simpan</button>
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
                    <!-- AKHIR MODAL -->
                    
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
        var table = $('#table_fpku').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('data-fpku.index') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'preview_undangan',name: 'preview_undangan'},
                {data: 'tgl_kegiatan',name: 'tgl_kegiatan',
                    render: function (data, type, row) {
                        return moment(row.tgl_kegiatan).format("DD MMM YYYY")
                    }
                },
                {data: 'nama_pegawai',name: 'nama_pegawai'},
                {data: 'action',name: 'action'},
            ]
        });
    });

    //TOMBOL TAMBAH DATA
    $('#tombol-tambah').click(function () {
        $('#button-simpan').val("create-post");
        $('#id').val('');
        $('#form-tambah-edit').trigger("reset");
        $('#modal-judul').html("Add new data");
        $('#tambah-edit-modal').modal('show');
    });

    // TOMBOL TAMBAH
    if ($("#form-tambah-edit").length > 0) {
        $("#form-tambah-edit").validate({
            submitHandler: function (form) {
                var actionType = $('#tombol-simpan').val();
                var formData = new FormData($("#form-tambah-edit")[0]);
                $('#tombol-simpan').html('Saving..');

                $.ajax({
                    data: formData,
                    contentType: false,
                    processData: false,
                    url: "{{ route('data-fpku.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-tambah-edit').trigger("reset");
                        $('#tambah-edit-modal').modal('hide');
                        $('#tombol-simpan').html('Save');
                        $('#table_fpku').DataTable().ajax.reload(null, true);
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
                        $('#noUndanganErrorMsg').text(response.responseJSON.errors.no_surat_undangan);
                        $('#undanganDariErrorMsg').text(response.responseJSON.errors.undangan_dari);
                        $('#namaKegiatanErrorMsg').text(response.responseJSON.errors.nama_kegiatan);
                        $('#tglKegiatanErrorMsg').text(response.responseJSON.errors.tgl_kegiatan);
                        $('#idPegawaiErrorMsg').text(response.responseJSON.errors.id_pegawai);
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

    // EDIT DATA
    $('body').on('click', '.edit-post', function () {
        var data_id = $(this).data('id');
        $.get('data-fpku/' + data_id + '/edit', function (data) {
            $('#modal-judul').html("Edit data");
            $('#tombol-simpan').val("edit-post");
            $('#tambah-edit-modal').modal('show');
              
            $('#id').val(data.id);
            // $('#nama_prodi').val(data.nama_prodi);
            // $('#kode_prodi').val(data.kode_prodi);
            // $('#id_fakultas').val(data.id_fakultas);
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
                        url: "data-fpku/" + dataId,
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
                        $('#table_fpku').DataTable().ajax.reload(null, true);
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

    var i = 1;
    $("#tombol-add-keperluan").click(function(){
        ++i;

        $("#dynamicAdd").append('<tr><td><button type="button" class="btn btn-danger remove-tr"><i class="bx bx-trash"></i></button></td><td><input type="text" class="form-control" id="isi_keperluan" name="kolom['+i+'][isi_keperluan]" placeholder="(isi keperluan '+i+')" value="" /></td></tr>');
    });

    $(document).on('click', '.remove-tr', function(){  
        $(this).parents('tr').remove();
    });

    $('.addField').click(function(){
        $('.firstRow').parent().append(`
            <div class="col-sm-0">
                <table class="table table-borderless">
                    <tr>
                        <td><button type=""button" class="btn btn-danger mb-3 deleteRow"><i class="bx bx-trash bx-xs"></i></button></td>
                        <td><input type="text" name="nama_berkas[]" class="w-100 form-control" placeholder="input nama berkas"></td>
                        <td><input type="file" name="berkas[]" class="w-100 form-control" accept=".pdf, .jpeg, .png"></td>
                    </tr>

                </table>

            </div>
        `);
    });

    $(document).on('click','.deleteRow', function(){
        $(this).parent().parent().remove();
    });

    function cekTanggal() {
        let inputDate = new Date(document.getElementById('tgl_kegiatan').value);
        let currentDate = new Date();
        let diffInDays = Math.floor((inputDate - currentDate) / (1000 * 60 * 60 * 24));
        let tombolSimpan = document.getElementById('tombol-simpan');
        let tglKegiatanErrorMsg = document.getElementById('tglKegiatanErrorMsg');

        if (diffInDays <= 12) {
            alert('Tanggal kegiatan kurang dari 14 hari, tidak dapat dilanjutkan.');
            tombolSimpan.disabled = true;
            tglKegiatanErrorMsg.textContent = 'Tanggal kegiatan kurang dari 14 hari.';
        } else {
            tombolSimpan.disabled = false;
            tglKegiatanErrorMsg.textContent = '';
        }
        }

        document.getElementById('cek_tanggal').addEventListener('change', function() {
        if (this.checked) {
            cekTanggal();
        } else {
            document.getElementById('tombol-simpan').disabled = false;
            document.getElementById('tglKegiatanErrorMsg').textContent = '';
        }
        });

        document.getElementById('tgl_kegiatan').addEventListener('change', function() {
        if (document.getElementById('cek_tanggal').checked) {
            cekTanggal();
        }
    });

</script>

@endsection