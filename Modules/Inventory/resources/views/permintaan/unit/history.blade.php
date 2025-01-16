@extends('inventory::layouts.master', ['title' => 'History Permintaan'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a href="javascript:void(0)" class="btn btn-info" title="Filter" data-toggle="modal"
                        data-target="#filterModal">Filter</a>
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

    <div class="modal fade" id="filterModal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Data Permintaan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('inventory.permintaan.history') }}" method="GET" class="form-row">
                        <div class="form-group col-md-6">
                            <label for="s_date">Tanggal Awal</label>
                            <input type="date" name="s_date" id="s_date" class="form-control"
                                value="{{ request('s_date') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="e_date">Tanggal Akhir</label>
                            <input type="date" name="e_date" id="e_date" class="form-control"
                                value="{{ request('e_date') }}" {{ !request('s_date') ? 'disabled' : '' }}>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="unit">Unit</label>
                            <select name="unit" id="unit" class="form-control">
                                <option value="">-- Pilih Unit --</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ request('unit') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->nama_unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            {{-- @if (request()->hasAny(['s_date', 'e_date', 'unit'])) --}}
                            <a href="{{ route('inventory.permintaan.history') }}" class="btn btn-secondary">Reset
                                Filter</a>
                            {{-- @endif --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#tblhistory').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('inventory.permintaan.history.data') }}",
                    data: function(d) {
                        d.s_date = $('#s_date').val();
                        d.e_date = $('#e_date').val();
                        d.unit = $('#unit').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode_prefix',
                        name: 'kode_prefix'
                    },
                    {
                        data: 'tanggal_permintaan_format',
                        name: 'tanggal_permintaan'
                    },
                    {
                        data: 'ruangan.unit.nama_unit',
                        name: 'ruangan.unit.nama_unit'
                    },
                    {
                        data: 'ruangan.nama_ruangan',
                        name: 'ruangan.nama_ruangan'
                    },
                    {
                        data: 'status_format',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Refilter the table
            $('#s_date, #e_date, #unit').on('change', function() {
                table.draw();
            });

            // Ganti event handler .btndetail dengan delegasi event
            $(document).on('click', '.btndetail', function() {
                let id = $(this).data('id');
                console.log(id);

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
                $('#detailModalLabel').html('DAta Barang Permintaan');
            });

            // Enable/disable end date based on start date
            $('#s_date').on('change', function() {
                if ($(this).val()) {
                    $('#e_date').prop('disabled', false);
                } else {
                    $('#e_date').prop('disabled', true);
                    $('#e_date').val('');
                }
            });

            // Prevent end date being before start date
            $('#e_date').on('change', function() {
                var startDate = $('#s_date').val();
                var endDate = $(this).val();

                if (startDate && endDate && endDate < startDate) {
                    alert('Tanggal akhir tidak boleh lebih kecil dari tanggal awal');
                    $(this).val('');
                }
            });
        });
    </script>
@endpush
