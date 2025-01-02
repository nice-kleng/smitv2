@extends('inventory::layouts.master', ['title' => 'Data Pengajuan'])

@section('button-header')
    @if (auth()->user()->hasRole('admin'))
        <a href="{{ route('inventory.pengajuan.create') }}" class="btn btn-primary btn-sm" title="Buat Pengajuan"><i
                class="fas fa-plus"></i></a>
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Pengajuan</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-stripped text-nowrap" id="pengajuanTable"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Pengajuan</th>
                                    <th>Unit</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Status Pengajuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->kode_prefix }}</td>
                                        <td>{{ $item->unit_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->locale('id')->isoFormat('DD MMMM YYYY') }}
                                        </td>
                                        @php
                                            $statusBadges = [
                                                0 => ['badge-warning', 'Proses'],
                                                1 => ['badge-danger', 'Ditolak'],
                                                2 => ['badge-info', 'Disetujui'],
                                                'default' => ['badge-success', 'Barang Sudah Datang'],
                                            ];
                                            $status = $statusBadges[$item->status] ?? $statusBadges['default'];
                                        @endphp
                                        <td>
                                            <span class="badge {{ $status[0] }}">{{ $status[1] }}</span>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm btn-detail"
                                                title="Lihat Detail Pengajuan" data-id="{{ $item->kode_prefix }}"><i
                                                    class="fas fa-eye"></i></a>
                                            @if (auth()->user()->hasRole('admin') && $item->status == 0)
                                                <a href="{{ route('inventory.pengajuan.edit', ['prefix' => $item->kode_prefix]) }}"
                                                    class="btn btn-warning btn-sm" title="Edit Pengajuan"><i
                                                        class="fas fa-edit"></i></a>
                                                <button type="button" class="btn btn-danger btn-sm" title="Hapus Pengajuan"
                                                    onclick="deleteData({{ $item->id }})"><i
                                                        class="fas fa-trash"></i></button>
                                            @endif

                                            @if (auth()->user()->hasRole('keuangan'))
                                                <a href="{{ route('inventory.pengajuan.approve', $item->kode_prefix) }}"
                                                    class="btn btn-warning btn-sm" title="Proses Approval">
                                                    Proses Approval
                                                </a>
                                            @endif
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

    {{-- Modal detail pengajuan --}}
    <div class="modal fade" id="detailModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
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
                                    <th>Kode Pengajuan</th>
                                    <th>Barang</th>
                                    <th>Jumlah Permintaan</th>
                                    <th>Jumlah Approve</th>
                                    <th>Harga</th>
                                    <th>Harga Approved</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Tanggal Approved</th>
                                    <th>Status</th>
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
            let detailTable = $('#tbldetail').DataTable({
                responsive: true,
                autoWidth: false,
                searching: true,
                ordering: true,
                paging: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                columnDefs: [{
                    targets: '_all',
                    defaultContent: '-'
                }]
            });

            $('.btn-detail').on('click', function() {
                let id = $(this).data('id');

                $.ajax({
                    type: "get",
                    url: "{{ route('inventory.pengajuan.show', ':id') }}".replace(':id', id),
                    dataType: "json",
                    success: function(response) {
                        detailTable.clear();

                        response.forEach((item, index) => {
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
                            let tanggalPengajuan = new Date(item.tanggal_pengajuan)
                                .toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'long',
                                    year: 'numeric'
                                });
                            let tanggalApproved = item.tanggal_approved ? new Date(item
                                .tanggal_approved).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            }) : '-';
                            let harga = new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR'
                            }).format(item.harga);
                            let hargaApproved = item.harga_approved ? new Intl
                                .NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR'
                                }).format(item.harga_approved) : '-';

                            detailTable.row.add([
                                index + 1,
                                item.kode_pengajuan,
                                item.barang.nama_barang,
                                item.jumlah,
                                item.jumlah_approved ?? 0,
                                harga,
                                hargaApproved,
                                tanggalPengajuan,
                                tanggalApproved,
                                `<span class="badge badge-${badge}">${text.charAt(0).toUpperCase() + text.slice(1).toLowerCase()}</span>`
                            ]);
                        });

                        detailTable.draw();
                        $('#detailModalLabel').text(
                            `Detail Pengajuan Barang - ${response[0].kode_pengajuan.slice(0, -3)}`
                        );
                        $('#detailModal').modal('show');
                    }
                });
            });
        });
    </script>
@endpush
