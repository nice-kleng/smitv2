@extends('inventory::layouts.master', ['title' => 'Data Permintaan'])

@section('button-header')
    @if (auth()->user()->hasRole('unit'))
        <a href="{{ route('inventory.permintaan.create') }}" class="btn btn-primary">Tambah Permintaan</a>
    @endif
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            Data Permintaan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-nowrap" id="permintaanTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Prefix</th>
                            <th>Tanggal Permintaan</th>
                            <th>Unit</th>
                            <th>Ruangan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permintaans as $permintaan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $permintaan->kode_prefix }}</td>
                                <td>{{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->locale('id')->isoFormat('DD MMMM YYYY') }}
                                </td>
                                <td>{{ $permintaan->ruangan->unit->nama_unit }}</td>
                                <td>{{ $permintaan->ruangan->nama_ruangan }}</td>
                                <td>
                                    @php
                                        $statusMap = [
                                            '0' => ['badge' => 'warning', 'text' => 'Menunggu Approval'],
                                            '1' => ['badge' => 'danger', 'text' => 'Ditolak'],
                                            '2' => ['badge' => 'primary', 'text' => 'Disetujui'],
                                            '3' => ['badge' => 'success', 'text' => 'Barang Sudah Diambil'],
                                        ];
                                        $badge = $statusMap[$permintaan->status]['badge'];
                                        $text = $statusMap[$permintaan->status]['text'];
                                    @endphp
                                    <span class="badge badge-{{ $badge }}">{{ Str::ucfirst($text) }}</span>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="btn btn-info btn-sm btndetail"
                                        title="Detail Permintaan" data-id="{{ $permintaan->kode_prefix }}">
                                        Detail
                                    </a>
                                    @if (auth()->user()->hasRole('unit') && $permintaan->status == '0')
                                        <a href="{{ route('inventory.permintaan.edit', $permintaan->kode_prefix) }}"
                                            class="btn btn-warning btn-sm" title="Edit Permintaan">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form
                                            action="{{ route('inventory.permintaan.destroy', ['id' => $permintaan->kode_prefix]) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" title="Hapus Permintaan"
                                                onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('inventory.permintaan.unduh-form-permintaan', $permintaan->kode_prefix) }}"
                                            class="btn btn-success btn-sm" target="_blank" title="Cetak Form Permintaan">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') && auth()->user()->can('approve-permintaan'))
                                        @if ($permintaan->status == '0')
                                            <a href="{{ route('inventory.permintaan.approve', $permintaan->kode_prefix) }}"
                                                class="btn btn-warning btn-sm" title="Proses Approval">
                                                Proses Approval
                                            </a>
                                        @else
                                            <a href="javascript:void(0)" class="btn btn-success btn-sm btnAmbil"
                                                data-prefix="{{ $permintaan->kode_prefix }}"
                                                title="Proses Pengambilan Barang">
                                                Diambil
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Data tidak ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal detail permintaan --}}
    <div class="modal fade" id="detailModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Barang Permintaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-header table-stripped text-nowrap" id="tbldetail">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Permintaan</th>
                                    <th>Barang</th>
                                    <th>Jumlah Permintaan</th>
                                    <th>Jumlah Approve</th>
                                    <th>Status</th>
                                    <th>Tanggal Permintaan</th>
                                    <th>Keperluan</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    {{-- <button type="button" class="btn btn-primary">Oke</button> --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal ambil barang --}}
    <div class="modal fade" id="modalAmbil" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Form Pengambilan barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="formAmbil">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="penerima">Penerima</label>
                            <input type="text" class="form-control" name="penerima" id="penerima" required>
                        </div>
                        <div id="barang_inv">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.btndetail').on('click', function() {
                let id = $(this).data('id');

                $.ajax({
                    type: "get",
                    url: `{{ route('inventory.permintaan.show', ':id') }}`.replace(':id', id),
                    dataType: "json",
                    success: function(response) {
                        let data = response.data;
                        let html = '';
                        data.forEach((item, index) => {
                            let statusMap = {
                                '0': {
                                    badge: 'warning',
                                    text: 'Menunggu Approval'
                                },
                                '1': {
                                    badge: 'danger',
                                    text: 'Ditolak'
                                },
                                '2': {
                                    badge: 'primary',
                                    text: 'Disetujui'
                                },
                                '3': {
                                    badge: 'success',
                                    text: 'Barang Sudah Diambil'
                                },
                            };
                            let badge = statusMap[item.status].badge;
                            let text = statusMap[item.status].text;
                            html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.kode_permintaan}</td>
                                <td>${item.barang.nama_barang}</td>
                                <td>${item.jumlah}</td>
                                <td>${item.jumlah_approve ?? '-'}</td>
                                <td>
                                    <span class="badge badge-${badge}">${text.charAt(0).toUpperCase() + text.slice(1).toLowerCase()}</span>
                                </td>
                                <td>${new Date(item.tanggal_permintaan).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}</td>
                                <td>${item.keterangan ?? '-'}</td>
                            </tr>
                        `;
                        });
                        $('#tbldetail tbody').html(html);
                        $('#detailModalLabel').text(
                            `Detail Barang Permintaan ${data[0].kode_permintaan}`);
                        $('#detailModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan, silahkan coba lagi');
                    }
                });
            });

            $('#detailModal').on('hidden.bs.modal', function() {
                $('#tbldetail tbody').html('');
                $('#detailModalLabel').html('Data Barang Permintaan');
            });

            $('.btnAmbil').on('click', function() {
                let prefix = $(this).data('prefix');
                $.ajax({
                    type: "get",
                    url: "{{ route('inventory.permintaan.get-inventory', ':prefix') }}".replace(
                        ':prefix', prefix),
                    dataType: "json",
                    success: function(response) {
                        $('#barang_inv').html(response.data);
                        $('#formAmbil').attr('action',
                            "{{ route('inventory.permintaan.proses-pengambilan', ':prefix') }}"
                            .replace(':prefix', prefix));
                        $('#modalAmbil').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseJSON);

                    }
                });
            });

            $('#modalAmbil').on('hidden.bs.modal', function() {
                $('#barang_inv').html('');
                $('#formAmbil').attr('action', '');
            });
        });
    </script>
@endpush
