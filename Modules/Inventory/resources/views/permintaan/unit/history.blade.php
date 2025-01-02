@extends('inventory::layouts.master', ['title' => 'History Permintaan'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Hostory Permintaan
                        {{ auth()->user()->hasRole('unit') ? auth()->user()->unit->nama_unit : '' }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-stripped" id="tblhistory">
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
                                @foreach ($history as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->kode_prefix }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_permintaan)->locale('id')->isoFormat('DD MMMM YYYY') }}
                                        </td>
                                        <td>{{ $item->ruangan->unit->nama_unit }}</td>
                                        <td>{{ $item->ruangan->nama_ruangan }}</td>
                                        <td>
                                            @php
                                                $statusMap = [
                                                    '0' => ['badge' => 'warning', 'text' => 'Menunggu Approval'],
                                                    '1' => ['badge' => 'danger', 'text' => 'Ditolak'],
                                                    '2' => ['badge' => 'primary', 'text' => 'Disetujui'],
                                                    '3' => ['badge' => 'success', 'text' => 'Barang Sudah Diambil'],
                                                ];
                                                $badge = $statusMap[$item->status]['badge'];
                                                $text = $statusMap[$item->status]['text'];
                                            @endphp
                                            <span class="badge badge-{{ $badge }}">{{ Str::ucfirst($text) }}</span>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm btndetail"
                                                title="Detail Permintaan" data-id="{{ $item->kode_prefix }}">
                                                Detail
                                            </a>
                                            <a href="{{ route('inventory.permintaan.unduh-form-permintaan', $item->kode_prefix) }}"
                                                class="btn btn-success btn-sm" target="_blank"
                                                title="Cetak Form Permintaan">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
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
                        console.log(response);

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
                $('#detailModalLabel').html('DAta Barang Permintaan');
            });
        });
    </script>
@endpush
