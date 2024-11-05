@extends('layouts.backend')
@section('title','Ajukan Proposal')

@section('breadcrumbs')
<div class="container-xxl">
<nav aria-label="breadcrumb mb-0">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="javascript:void(0)">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{route('submission-of-proposal.index')}}">@yield('title')</a>
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

<div class="container-xxl flex-grow-1">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card table-responsive">
                    <div class="card-body">
                        <!-- MULAI TOMBOL TAMBAH -->
                        @if($checkLap->count() > 0)
                            @foreach($checkLap as $p)  @endforeach
                                @if($p->status_laporan == '')
                                    <div class="mb-3">
                                        <a href="javascript:void(0)" class="dropdown-shortcuts-add text-muted"><button type="button" class="btn btn-outline-secondary" onclick="alert('Anda dapat mengajukan proposal baru setelah menyelesaikan laporan pertanggung-jawaban proposal Anda sebelumnya dan telah di verifikasi oleh Rektorat! Mohon periksa kembali status proposal Anda.')"><i class="bx bx-plus-circle bx-spin-hover"></i> Proposal Baru</button></a>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <a href="{{route('tampilan-proposal-baru')}}" class="dropdown-shortcuts-add text-body" id="proposal-baru"><button type="button" class="btn btn-outline-primary"><i class="bx bx-plus-circle bx-spin-hover"></i> Proposal Baru</button></a>
                                    </div>
                                @endif
                           
                        @else
                            <div class="mb-3">
                                <a href="{{route('tampilan-proposal-baru')}}" class="dropdown-shortcuts-add text-body" id="proposal-baru"><button type="button" class="btn btn-outline-primary"><i class="bx bx-plus-circle bx-spin-hover"></i> Proposal Baru</button></a>
                            </div>
                        @endif
                        <!-- AKHIR TOMBOL -->
                        <table class="table table-hover table-responsive" id="table_proposal">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Preview</th>
                                <th>Proposal</th>
                                <th>Nama Kegiatan</th>
                                <th>Nama Pengaju</th>
                                <th width="12%;">Status</th>
                                <th width="12%;">Aksi</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    
                     <!-- Mulai modal detail -->
                     <div class="modal fade" tabindex="-1" role="dialog" id="show-detail" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">Progres Timeline Proposal</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table" class="col-sm-12 table-responsive mb-3"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of modal detail-->

                    {{-- Info detil keterangan --}}
                    <div class="modal animate__animated animate__swing mt-3" tabindex="-1" role="dialog" id="keterangan-modal" aria-hidden="true">
                        <div class="modal-dialog ">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-ket"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="mb-3">
                                            <textarea class="form-control" id="detil_ket" name="detil_ket" rows="10" readonly></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Info detil keterangan --}}

                    <div class="modal fade mt-3" tabindex="-1" role="dialog" id="pushedit-modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-edit">Edit Sarpras</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-edit-sarpras" name="form-edit-sarpras" class="form-horizontal">
                                        <input type="hidden" id="e_sarpras_id" name="e_sarpras_id" class="form-control">
                                        <div class="col-sm-12">
                                            <div class="col-sm-4 mb-2">
                                                <label for="e_tgl_kegiatan" class="form-label">Tgl Kegiatan</label>
                                                <input type="date" class="form-control" id="e_tgl_kegiatan" name="e_tgl_kegiatan" value="" placeholder="mm/dd/yyyy" />
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label for="e_sarpras_item" class="form-label">Item</label>
                                            <textarea class="form-control" id="e_sarpras_item" name="e_sarpras_item" rows="4"></textarea>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <div class="col-sm-6 mb-2">
                                                    <label for="e_jumlah" class="form-label">Jumlah</label>
                                                    <input type="number" class="form-control" id="e_jumlah" name="e_jumlah" value="" />
                                                </div>                                            
                                                <div class="col-sm-6 mb-2">
                                                    <label for="e_sumber" class="form-label">Sumber dana</label>
                                                    <select class="select2 form-select" id="e_sumber" name="e_sumber" style="cursor:pointer;">
                                                        <option value="1">Kampus</option>
                                                        <option value="2">Mandiri</option>
                                                        <option value="3">Hibah</option>
                                                      </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-offset-2 col-sm-12">
                                            <hr class="mt-2">
                                            <div class="float-sm-end">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary btn-block" id="btn-update" value="create">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mulai modal lihat lampiran -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="show-lampiran" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">Lampiran Proposal</h5>
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

                     <!-- Mulai modal lihat anggaran -->
                     <div class="modal fade" tabindex="-1" role="dialog" id="show-anggaran" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">Anggaran Proposal</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table_show_anggaran" class="col-sm-12 table-responsive mb-3"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of modal lihat anggaran-->
                    
                    <!-- modal edit anggaran-->
                    <div class="modal fade mt-3" tabindex="-1" role="dialog" id="editanggaran-modal" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-edit-anggaran">Edit Anggaran</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-edit-anggaran" name="form-edit-anggaran" class="form-horizontal">
                                        <input type="hidden" id="props_id" name="props_id" class="form-control">  
                                        <input type="hidden" id="e_anggaran_id" name="e_anggaran_id" class="form-control">  
                                        <div class="mb-2">
                                            <label for="e_anggaran_item" class="form-label">Item</label>
                                            <input type="text" class="form-control" id="e_anggaran_item" name="e_anggaran_item" value="" />
                                        </div>  
                                        <div class="mb-2">
                                            <label for="e_anggaran_biaya_satuan" class="form-label">Biaya Satuan</label>
                                            <input type="number" min="0" class="form-control" id="e_anggaran_biaya_satuan" name="e_anggaran_biaya_satuan" value="" />
                                        </div>
                                        <div class="mb-2">
                                            <label for="e_anggaran_quantity" class="form-label">Jumlah (Qty)</label>
                                            <input type="number" min="0" class="form-control" id="e_anggaran_quantity" name="e_anggaran_quantity" value="" />
                                        </div>   
                                        <div class="mb-2">
                                            <label for="e_anggaran_frequency" class="form-label">Frekuensi</label>
                                            <input type="number" min="0" class="form-control" id="e_anggaran_frequency" name="e_anggaran_frequency" value="" />
                                        </div>   
                                        <div class="mb-2">
                                            <label for="e_anggaran_sumber_dana" class="form-label">Sumber dana</label>
                                            <select class="select2 form-select" id="e_anggaran_sumber_dana" name="e_anggaran_sumber_dana" style="cursor:pointer;">
                                                <option value="1">Kampus</option>
                                                <option value="2">Mandiri</option>
                                                <option value="3">Hibah</option>
                                              </select>
                                        </div>
                                        <div class="col-sm-offset-2 col-sm-12">
                                            <hr class="mt-2">
                                            <div class="float-sm-end">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary btn-block" id="btn-update-anggaran" value="create">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end of modal edit anggaran-->

                    <!-- Mulai modal lihat informasi -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="show-informasi" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title justify-content-center">Informasi Proposal</h5>                                    
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="table_show_informasi" class="col-sm-12 table-responsive mb-3"></div>
                                    <input type="hidden" id="edit_id" name="edit_id">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="checkbox" id="check_finish" class="form-check-input"><small>&nbsp;&nbsp;<i>Jika anda telah melakukan revisi, silakan centang lalu submit ulang proposal anda.</i></small>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mt-2 float-end">
                                                <button type="button" class="btn btn-primary" id="submit-ulang"><i class="bx bx-check-circle bx-tada-hover"></i> Submit ulang</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of modal lihat informasi-->

                    {{-- EDIT INFORMASI PROPOSAL --}}
                    <!-- modal edit nama kegiatan-->
                    <div class="modal fade mt-3" tabindex="-1" role="dialog" id="editnamakegiatan-modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-edit-nama-kegiatan">Edit Nama Kegiatan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-edit-nama-kegiatan" name="form-edit-nama-kegiatan" class="form-horizontal">
                                        <input type="hidden" id="props_id_nama_kegiatan" name="props_id_nama_kegiatan" class="form-control">  
                                        <div class="mb-2">
                                            <label for="e_nama_kegiatan" class="form-label">Nama Kegiatan</label>
                                            <input type="text" class="form-control" id="e_nama_kegiatan" name="e_nama_kegiatan" value="" />
                                        </div>  
                                        <div class="col-sm-offset-2 col-sm-12">
                                            <hr class="mt-2">
                                            <div class="float-sm-end">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary btn-block" id="btn-update-nama-kegiatan" value="create">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade mt-3" tabindex="-1" role="dialog" id="editpendahuluan-modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-edit-pendahuluan">Edit Pendahuluan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-edit-pendahuluan" name="form-edit-pendahuluan" class="form-horizontal">
                                        <input type="hidden" id="props_id_pendahuluan" name="props_id_pendahuluan" class="form-control">  
                                        <div class="mb-2">
                                            <label for="e_pendahuluan" class="form-label">Pendahuluan</label>
                                            <div id="editor-pendahuluan" class="mb-3" style="height: 300px;"></div>
                                            <textarea rows="3" class="mb-3 d-none" name="e_pendahuluan" id="e_pendahuluan"></textarea>
                                        </div>  
                                        <div class="col-sm-offset-2 col-sm-12">
                                            <hr class="mt-2">
                                            <div class="float-sm-end">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary btn-block" id="btn-update-pendahuluan" value="create">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade mt-3" tabindex="-1" role="dialog" id="edittujuanmanfaat-modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-edit-tujuan-manfaat">Edit Tujuan dan Manfaat</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-edit-tujuan-manfaat" name="form-edit-tujuan-manfaat" class="form-horizontal">
                                        <input type="hidden" id="props_id_tujuan_manfaat" name="props_id_tujuan_manfaat" class="form-control">  
                                        <div class="mb-2">
                                            <label for="e_tujuan_manfaat" class="form-label">Tujuan dan Manfaat</label>
                                            <div id="editor-tujuan-manfaat" class="mb-3" style="height: 300px;"></div>
                                            <textarea rows="3" class="mb-3 d-none" name="e_tujuan_manfaat" id="e_tujuan_manfaat"></textarea>
                                        </div>  
                                        <div class="col-sm-offset-2 col-sm-12">
                                            <hr class="mt-2">
                                            <div class="float-sm-end">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary btn-block" id="btn-update-tujuan-manfaat" value="create">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade mt-3" tabindex="-1" role="dialog" id="edittglevent-modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-edit-tglevent">Edit Tujuan dan Manfaat</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-edit-tglevent" name="form-edit-tglevent" class="form-horizontal">
                                        <input type="hidden" id="props_id_tglevent" name="props_id_tglevent" class="form-control">  
                                        <div class="mb-2">
                                            <label for="e_tglevent" class="form-label">Tujuan dan Manfaat</label>
                                            <input type="date" class="form-control" id="e_tglevent" name="e_tglevent" value="" placeholder="mm/dd/yyyy" />
                                        </div>  
                                        <div class="col-sm-offset-2 col-sm-12">
                                            <hr class="mt-2">
                                            <div class="float-sm-end">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary btn-block" id="btn-update-tglevent" value="create">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade mt-3" tabindex="-1" role="dialog" id="editlokasitempat-modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-edit-lokasitempat">Edit Lokasi atau Tempat</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-edit-lokasitempat" name="form-edit-lokasitempat" class="form-horizontal">
                                        <input type="hidden" id="props_id_lokasitempat" name="props_id_lokasitempat" class="form-control">  
                                        <div class="mb-2">
                                            <label for="e_lokasitempat" class="form-label">Lokasi atau Tempat</label>
                                            <input type="text" class="form-control" id="e_lokasitempat" name="e_lokasitempat" value="" />
                                        </div>  
                                        <div class="col-sm-offset-2 col-sm-12">
                                            <hr class="mt-2">
                                            <div class="float-sm-end">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary btn-block" id="btn-update-lokasitempat" value="create">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade mt-3" tabindex="-1" role="dialog" id="editpeserta-modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-edit-peserta">Edit Peserta</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-edit-peserta" name="form-edit-peserta" class="form-horizontal">
                                        <input type="hidden" id="props_id_peserta" name="props_id_peserta" class="form-control">  
                                        <div class="mb-2">
                                            <label for="e_peserta" class="form-label">Peserta</label>
                                            <div id="editor-peserta" class="mb-3" style="height: 300px;"></div>
                                            <textarea rows="3" class="mb-3 d-none" name="e_peserta" id="e_peserta"></textarea>
                                        </div>  
                                        <div class="col-sm-offset-2 col-sm-12">
                                            <hr class="mt-2">
                                            <div class="float-sm-end">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary btn-block" id="btn-update-peserta" value="create">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade mt-3" tabindex="-1" role="dialog" id="editdetilkegiatan-modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-judul-edit-detilkegiatan">Edit Detil Kegiatan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-edit-detilkegiatan" name="form-edit-detilkegiatan" class="form-horizontal">
                                        <input type="hidden" id="props_id_detilkegiatan" name="props_id_detilkegiatan" class="form-control">  
                                        <div class="mb-2">
                                            <label for="e_detil_kegiatan" class="form-label">Detil Kegiatan</label>
                                            <div id="editor-detilkegiatan" class="mb-3" style="height: 300px;"></div>
                                            <textarea rows="3" class="mb-3 d-none" name="e_detilkegiatan" id="e_detilkegiatan"></textarea>
                                        </div>  
                                        <div class="col-sm-offset-2 col-sm-12">
                                            <hr class="mt-2">
                                            <div class="float-sm-end">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary btn-block" id="btn-update-detilkegiatan" value="create">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <!-- end of modal edit nama kegiatan-->

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
            ajax: "{{ route('submission-of-proposal.index') }}",
            columns: [
                {data: null,sortable:false,
                    render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, 
                {data: 'laporan',name: 'laporan'},
                {data: 'nama_jenis_kegiatan',name: 'nama_jenis_kegiatan'},
                {data: 'nama_kegiatan',name: 'nama_kegiatan'},
                {data: 'nama_user',name: 'nama_user',
                    render: function(data,type,row){
                        return row.nama_user_dosen || row.nama_user_mahasiswa
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

    $('body').on('click','.alasan', function(){
        var data_ket = $(this).attr('data-alasan');
        $('#modal-judul-ket').html("Detil keterangan");
        $('#keterangan-modal').modal('show');
        $('#detil_ket').val(data_ket);
    });

    $('body').on('click','.edit-post', function(){
        var dataId = $(this).data('id');
        var dataTgl = $(this).attr('data-tgl');
        var dataItem = $(this).attr('data-item');
        var dataJumlah = $(this).attr('data-jumlah');
        var dataSumber = $(this).attr('data-sumber');
        $('#modal-judul-edit').html("Edit Sarpras");
        $('#pushedit-modal').modal('show');
        $('#e_sarpras_id').val(dataId);
        $('#e_tgl_kegiatan').val(dataTgl);
        $('#e_sarpras_item').val(dataItem);
        $('#e_jumlah').val(dataJumlah);
        $('#e_sumber').val(dataSumber);
    });
    if ($("#form-edit-sarpras").length > 0) {
        $("#form-edit-sarpras").validate({
            submitHandler: function (form) {
                var actionType = $('#btn-update').val();
                $('#btn-update').html('Updating..');

                $.ajax({
                    data: $('#form-edit-sarpras').serialize(),
                    url: "{{ route('update-pegajuan-sarpras') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-edit-sarpras').trigger("reset");
                        $('#pushedit-modal').modal('hide');
                        $('#btn-update').html('Update');
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
                        location.reload();
                    },
                    error: function(response) {
                        $('#btn-update').html('Update');
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
            }
        })
    }


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
                        url: "submission-of-proposal/" + dataId,
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

    $('body').on('click','.v-lampiran', function(){
        var data_id = $(this).data('id');
        $.ajax({
            url: "{{route('view-lampiran-proposal')}}",
            method: "GET",
            data: {
                proposal_id: data_id,  
                "_token": "{{ csrf_token() }}",
            },
            success: function(response, data){
                $('#show-lampiran').modal('show');
                $("#table_lampiran").html(response.card)
            }
        })
    });

    // TOMBOL DELETE ITEM SARPRAS
    $(document).on('click', '.delete-post', function () {
        dataId = $(this).data('id');
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
                        url: "{{route('delete-item-sarpras')}}",
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
                        location.reload();
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

    $(document).on('click','.lihat-anggaran', function(){
        dataId = $(this).data('id');
        $.ajax({
            url: "{{route('check-anggaran-proposal')}}",
            method: "GET",
            data: {proposal_id: dataId},
            success: function(response, data){
                $('#show-anggaran').modal('show');
                $("#table_show_anggaran").html(response.card)
            }
        })
    });

    // Edit Anggaran
    $('body').on('click','.edit-anggaran', function(){
        var dataId = $(this).data('id');
        var idProps = $(this).attr('data-id-proposal');
        var dataItem = $(this).attr('data-item');
        var dataBiayaSatuan = $(this).attr('data-biaya-satuan');
        var dataQuantity = $(this).attr('data-quantity');
        var dataFrequency = $(this).attr('data-frequency');
        var dataSumberDana = $(this).attr('data-sumber-dana');
        $('#modal-judul-edit-anggaran').html("Edit Anggaran");
        $('#editanggaran-modal').modal('show');
        $('#props_id').val(idProps);
        $('#e_anggaran_id').val(dataId);
        $('#e_anggaran_item').val(dataItem);
        $('#e_anggaran_biaya_satuan').val(dataBiayaSatuan);
        $('#e_anggaran_quantity').val(dataQuantity);
        $('#e_anggaran_frequency').val(dataFrequency);
        $('#e_anggaran_sumber_dana').val(dataSumberDana);
    });
    if ($("#form-edit-anggaran").length > 0) {
        $("#form-edit-anggaran").validate({
            submitHandler: function (form) {
                var actionType = $('#btn-update-anggaran').val();
                $('#btn-update-anggaran').html('Updating..');

                $.ajax({
                    data: $('#form-edit-anggaran').serialize(),
                    url: "{{ route('update-anggaran-item') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#form-edit-anggaran').trigger("reset");
                        $('#pushedit-modal').modal('hide');
                        $('#btn-update-anggaran').html('Update');
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
                        location.reload();
                    },
                    error: function(response) {
                        $('#btn-update-anggaran').html('Update');
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
            }
        })
    }

    $(document).on('click', '.delete-anggaran-post', function () {
        dataId = $(this).data('id');
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
                        url: "{{route('delete-item-anggaran')}}",
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
                        location.reload();
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

    $(document).on('click','.lihat-informasi', function(){
        dataId = $(this).data('id');
        val_idProp = $('#edit_id').val(dataId);
        $.ajax({
            url: "{{route('check-informasi-proposal')}}",
            method: "GET",
            data: {proposal_id: dataId},
            success: function(response, data){
                $('#show-informasi').modal('show');
                $("#table_show_informasi").html(response.card)
            }
        })
    });

    // EDIT INFORMASI UTAMA
    $('body').on('click','.edit-nama-kegiatan', function(){
        var idProps = $(this).attr('data-id-proposal');
        var dataNamaKegiatan = $(this).attr('data-nama-kegiatan');
        $('#modal-judul-edit-nama-kegiatan').html("Edit Nama Kegiatan");
        $('#editnamakegiatan-modal').modal('show');
        $('#props_id_nama_kegiatan').val(idProps);
        $('#e_nama_kegiatan').val(dataNamaKegiatan);
    });
    $(document).on('click','#btn-update-nama-kegiatan', function(){
        var actionType = $('#btn-update-nama-kegiatan').val();
        $('#btn-update-nama-kegiatan').html('Updating..');
        $.ajax({
            data: $('#form-edit-nama-kegiatan').serialize(),
            url: "{{ route('update-nama-kegiatan') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('#form-edit-nama-kegiatan').trigger("reset");
                $('#editnamakegiatan-modal').modal('hide');
                $('#btn-update-nama-kegiatan').html('Update');
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
                $('#btn-update-nama-kegiatan').html('Update');
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

    $('body').on('click','.edit-pendahuluan', function(){
        var idProps = $(this).attr('data-id-proposal');
        var dataPendahuluan = $(this).attr('data-pendahuluan');
        $('#modal-judul-edit-pendahuluan').html("Edit Pendahuluan");
        $('#editpendahuluan-modal').modal('show');
        $('#props_id_pendahuluan').val(idProps);
        $('#e_pendahuluan').val(dataPendahuluan);
    });
    $(document).on('click','#btn-update-pendahuluan', function(){
        var actionType = $('#btn-update-pendahuluan').val();
        $('#btn-update-pendahuluan').html('Updating..');
        $.ajax({
            data: $('#form-edit-pendahuluan').serialize(),
            url: "{{ route('update-pendahuluan') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('#form-edit-pendahuluan').trigger("reset");
                $('#editpendahuluan-modal').modal('hide');
                $('#btn-update-pendahuluan').html('Update');
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
                $('#btn-update-pendahuluan').html('Update');
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

    $('body').on('click','.edit-tujuan-manfaat', function(){
        var idProps = $(this).attr('data-id-proposal');
        var dataTujuanManfaat = $(this).attr('data-tujuan-manfaat');
        $('#modal-judul-edit-tujuan-manfaat').html("Edit Tujuan dan Manfaat");
        $('#edittujuanmanfaat-modal').modal('show');
        $('#props_id_tujuan_manfaat').val(idProps);
        $('#e_tujuan_manfaat').val(dataTujuanManfaat);
    });
    $(document).on('click','#btn-update-tujuan-manfaat', function(){
        var actionType = $('#btn-update-tujuan-manfaat').val();
        $('#btn-update-tujuan-manfaat').html('Updating..');
        $.ajax({
            data: $('#form-edit-tujuan-manfaat').serialize(),
            url: "{{ route('update-tujuan-manfaat') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('#form-edit-tujuan-manfaat').trigger("reset");
                $('#edittujuanmanfaat-modal').modal('hide');
                $('#btn-update-tujuan-manfaat').html('Update');
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
                $('#btn-update-tujuan-manfaat').html('Update');
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

    $('body').on('click','.edit-tglevent', function(){
        var idProps = $(this).attr('data-id-proposal');
        var dataTglEvent = $(this).attr('data-tglevent');
        $('#modal-judul-edit-tglevent').html("Edit Tanggal Kegiatan");
        $('#edittglevent-modal').modal('show');
        $('#props_id_tglevent').val(idProps);
        $('#e_tglevent').val(dataTglEvent);
    });
    $(document).on('click','#btn-update-tglevent', function(){
        var actionType = $('#btn-update-tglevent').val();
        $('#btn-update-tglevent').html('Updating..');
        $.ajax({
            data: $('#form-edit-tglevent').serialize(),
            url: "{{ route('update-tglevent') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('#form-edit-tglevent').trigger("reset");
                $('#edittglevent-modal').modal('hide');
                $('#btn-update-tglevent').html('Update');
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
                $('#btn-update-tglevent').html('Update');
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

    $('body').on('click','.edit-lokasitempat', function(){
        var idProps = $(this).attr('data-id-proposal');
        var dataLokasiTempat = $(this).attr('data-lokasitempat');
        $('#modal-judul-edit-lokasitempat').html("Edit lokasi atau tempat");
        $('#editlokasitempat-modal').modal('show');
        $('#props_id_lokasitempat').val(idProps);
        $('#e_lokasitempat').val(dataLokasiTempat);
    });
    $(document).on('click','#btn-update-lokasitempat', function(){
        var actionType = $('#btn-update-lokasitempat').val();
        $('#btn-update-lokasitempat').html('Updating..');
        $.ajax({
            data: $('#form-edit-lokasitempat').serialize(),
            url: "{{ route('update-lokasitempat') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('#form-edit-lokasitempat').trigger("reset");
                $('#editlokasitempat-modal').modal('hide');
                $('#btn-update-lokasitempat').html('Update');
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
                $('#btn-update-lokasitempat').html('Update');
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

    $('body').on('click','.edit-peserta', function(){
        var idProps = $(this).attr('data-id-proposal');
        var dataPeserta = $(this).attr('data-peserta');
        $('#modal-judul-edit-peserta').html("Edit Peserta");
        $('#editpeserta-modal').modal('show');
        $('#props_id_peserta').val(idProps);
        $('#e_peserta').val(dataPeserta);
    });
    $(document).on('click','#btn-update-peserta', function(){
        var actionType = $('#btn-update-peserta').val();
        $('#btn-update-peserta').html('Updating..');
        $.ajax({
            data: $('#form-edit-peserta').serialize(),
            url: "{{ route('update-peserta') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('#form-edit-peserta').trigger("reset");
                $('#editpeserta-modal').modal('hide');
                $('#btn-update-peserta').html('Update');
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
                $('#btn-update-peserta').html('Update');
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

    $('body').on('click','.edit-detilkegiatan', function(){
        var idProps = $(this).attr('data-id-proposal');
        var dataDetilKegiatan = $(this).attr('data-detilkegiatan');
        $('#modal-judul-edit-detilkegiatan').html("Edit Detil Kegiatan");
        $('#editdetilkegiatan-modal').modal('show');
        $('#props_id_detilkegiatan').val(idProps);
        $('#e_detilkegiatan').val(dataDetilKegiatan);
    });
    $(document).on('click','#btn-update-detilkegiatan', function(){
        var actionType = $('#btn-update-detilkegiatan').val();
        $('#btn-update-detilkegiatan').html('Updating..');
        $.ajax({
            data: $('#form-edit-detilkegiatan').serialize(),
            url: "{{ route('update-detilkegiatan') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('#form-edit-detilkegiatan').trigger("reset");
                $('#editdetilkegiatan-modal').modal('hide');
                $('#btn-update-detilkegiatan').html('Update');
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
                $('#btn-update-detilkegiatan').html('Update');
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
        $('#modal-judul-edit-penutup').html("Edit Detil Kegiatan");
        $('#editpenutup-modal').modal('show');
        $('#props_id_penutup').val(idProps);
        $('#e_penutup').val(dataPenutup);
    });
    $(document).on('click','#btn-update-penutup', function(){
        var actionType = $('#btn-update-penutup').val();
        $('#btn-update-penutup').html('Updating..');
        $.ajax({
            data: $('#form-edit-penutup').serialize(),
            url: "{{ route('update-penutup') }}",
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
          if (document.getElementById('e_pendahuluan')) {
              var editor = new Quill('#editor-pendahuluan', {
                  theme: 'snow'
              });
              var quillEditor = document.getElementById('e_pendahuluan');
              editor.on('text-change', function() {
                  quillEditor.value = editor.root.innerHTML;
              });

              quillEditor.addEventListener('input', function() {
                  editor.root.innerHTML = quillEditor.value;
              });
          }

          if (document.getElementById('e_tujuan_manfaat')) {
              var editor1 = new Quill('#editor-tujuan-manfaat', {
                  theme: 'snow'
              });
              var quillEditor1 = document.getElementById('e_tujuan_manfaat');
              editor1.on('text-change', function() {
                  quillEditor1.value = editor1.root.innerHTML;
              });

              quillEditor1.addEventListener('input', function() {
                  editor1.root.innerHTML = quillEditor1.value;
              });
          }          

          if (document.getElementById('e_detilkegiatan')) {
              var editor2 = new Quill('#editor-detilkegiatan', {
                  theme: 'snow'
              });
              var quillEditor2 = document.getElementById('e_detilkegiatan');
              editor2.on('text-change', function() {
                  quillEditor2.value = editor2.root.innerHTML;
              });

              quillEditor2.addEventListener('input', function() {
                  editor2.root.innerHTML = quillEditor2.value;
              });
          }

          if (document.getElementById('e_penutup')) {
              var editor3 = new Quill('#editor-penutup', {
                  theme: 'snow'
              });
              var quillEditor3 = document.getElementById('e_penutup');
              editor3.on('text-change', function() {
                  quillEditor3.value = editor3.root.innerHTML;
              });

              quillEditor3.addEventListener('input', function() {
                  editor3.root.innerHTML = quillEditor3.value;
              });
          }

          if (document.getElementById('e_peserta')) {
              var editor4 = new Quill('#editor-peserta', {
                  theme: 'snow'
              });
              var quillEditor4 = document.getElementById('e_peserta');
              editor4.on('text-change', function() {
                  quillEditor4.value = editor4.root.innerHTML;
              });

              quillEditor4.addEventListener('input', function() {
                  editor4.root.innerHTML = quillEditor4.value;
              });
          }
    });

    $('body').on('click','.info-ditolakdekan',function(){
    var dataKet = $(this).attr('data-keteranganditolak');
    alert(dataKet);
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
            text: "Please check you have filled the whole documents. If you confirm to save permanently, you won't be able to change it anymore!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sure, confirm',
            showLoaderOnConfirm: true,
            preConfirm: function() {
                return new Promise(function(resolve) {
                    $.ajax({
                        url: "{{route('re-submit-proposal')}}",
                        type: 'POST',
                        data: {
                            id_proposal: $('#edit_id').val()
                        },
                        dataType: 'json'
                    }).done(function(response) {
                        Swal.fire({
                            title: 'Saved!',
                            text: 'Your data has been saved.',
                            type: 'success',
                            timer: 2000
                        })
                        location.reload();
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


</script>

@endsection