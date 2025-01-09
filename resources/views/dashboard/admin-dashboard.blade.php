@extends('layouts.backend')
@section('title','Dashboard')

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <h5>Hallo, @if (Str::length(Auth::guard('pegawai')->user()) > 0 )
                {{ Auth::guard('pegawai')->user()->nama_pegawai }}
                @elseif(Str::length(Auth::guard('mahasiswa')->user()) > 0)
                {{ Auth::guard('mahasiswa')->user()->name }}
                @endif</h5>
            <h4>Selamat datang di Dashboard Sistem Pengajuan Proposal Kegiatan</h4>
        </div>      
    
    </div>
    <!-- / Content -->

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
        var table = $('#table_sarpras').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('data-dash-admin') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'tgl_kegiatan',name: 'tgl_kegiatan'},
                {data: 'sarpras_item',name: 'sarpras_item'},
                {data: 'jumlah',name: 'jumlah'},
                {data: 'action',name: 'action'},
                {data: 'detil',name: 'detil'},
            ]
        });
    });

    $('body').on('click','.alasan', function(){
        var data_ket = $(this).attr('data-ket');
        $('#modal-judul-ket').html("Detil keterangan");
        $('#keterangan-modal').modal('show');
        $('#detil_ket').val(data_ket);
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
        var data_id = $(this).data('id');
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
                        $('#table_sarpras').DataTable().ajax.reload(null, true);
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
</script>
@endsection