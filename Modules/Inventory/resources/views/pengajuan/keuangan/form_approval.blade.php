@extends('inventory::layouts.master', ['title' => 'Approval Pengajuan ' . $pengajuans->first()->unit->nama_unit])

@section('button-header')
    <a href="{{ route('inventory.pengajuan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Approval Pengajuan {{ $kode }}</h4>
                </div>
                <form action="{{ route('inventory.pengajuan.process-approval', $kode) }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap" id="approval-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px">No</th>
                                        <th>Barang</th>
                                        <th style="width: 130px">Jumlah</th>
                                        <th style="width: 130px">Satuan</th>
                                        <th style="min-width: 130px">Harga</th>
                                        <th style="min-width: 130px">Total</th>
                                        <th style="width: 150px">Harga Disetujui</th>
                                        <th style="width: 150px">Jumlah Disetujui</th>
                                        <th style="min-width: 130px">Total Disetujui</th>
                                        <th style="min-width: 200px">Keterangan</th>
                                        <th style="width: 80px">
                                            <div class="form-check">
                                                <input class="form-check-input check-all" type="checkbox" id="checkAll">
                                                <label class="form-check-label" for="checkAll">
                                                    Setujui
                                                </label>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pengajuans as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->barang->nama_barang }}</td>
                                            <td class="text-center">{{ $item->jumlah }}</td>
                                            <td class="text-center">{{ $item->barang->satuan->nama_satuan }}</td>
                                            <td class="text-end">Rp. {{ number_format($item->harga) }}</td>
                                            <td class="text-end">Rp. {{ number_format($item->harga * $item->jumlah) }}</td>
                                            <td>
                                                <input type="number" name="harga_approve[{{ $item->id }}]"
                                                    class="form-control text-end harga-approve"
                                                    value="{{ old('harga_approve.' . $item->id, $item->harga) }}"
                                                    data-row="{{ $item->id }}">
                                            </td>
                                            <td>
                                                <input type="number" name="jumlah_approve[{{ $item->id }}]"
                                                    class="form-control text-center jumlah-approve"
                                                    value="{{ old('jumlah_approve.' . $item->id, $item->jumlah) }}"
                                                    data-row="{{ $item->id }}">
                                            </td>
                                            <td class="text-end">
                                                <span class="total-approve" id="total-{{ $item->id }}">Rp.
                                                    {{ number_format($item->harga * $item->jumlah) }}
                                                </span>
                                            </td>
                                            <td>
                                                <input type="text" name="keterangan_approve[{{ $item->id }}]"
                                                    class="form-control" placeholder="Keterangan">
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input approve-check" type="checkbox"
                                                        name="approve[{{ $item->id }}]" value="2">
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">Proses Approval</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Format Rupiah function
            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(number);
            }

            // Initialize DataTable
            const table = $('#approval-table').DataTable({
                pageLength: 10,
                scrollX: true,
                autoWidth: false,
                columnDefs: [{
                        orderable: false,
                        targets: [5, 6, 7, 8, 9]
                    },
                    {
                        width: '50px',
                        targets: 0
                    }, // No
                    {
                        width: '200px',
                        targets: 1
                    }, // Barang
                    {
                        width: '100px',
                        targets: 2
                    }, // Jumlah
                    {
                        width: '100px',
                        targets: 3
                    }, // Satuan
                    {
                        width: '130px',
                        targets: 4
                    }, // Harga
                    {
                        width: '130px',
                        targets: 5
                    }, // Total
                    {
                        width: '130px',
                        targets: 6
                    }, // Harga Disetujui
                    {
                        width: '130px',
                        targets: 7
                    }, // Jumlah Disetujui
                    {
                        width: '130px',
                        targets: 8
                    }, // Total Disetujui
                    {
                        width: '200px',
                        targets: 9
                    }, // Keterangan
                    {
                        width: '80px',
                        targets: 10
                    } // Checkbox
                ],
                order: [
                    [0, 'asc']
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                }
            });

            // Handle check all - modified for DataTables
            $('#checkAll').change(function() {
                const isChecked = $(this).prop('checked');
                $('.approve-check').prop('checked', isChecked);
            });

            // Calculate total on input change - works with DataTables
            $(document).on('input', '.harga-approve, .jumlah-approve', function() {
                const row = $(this).data('row');
                const harga = $(`input[name="harga_approve[${row}]"]`).val() || 0;
                const jumlah = $(`input[name="jumlah_approve[${row}]"]`).val() || 0;
                const total = harga * jumlah;
                $(`#total-${row}`).text(formatRupiah(total));
            });

            // Re-bind events after page change
            $('#approval-table').on('draw.dt', function() {
                const mainCheck = $('#checkAll').prop('checked');
                $('.approve-check').prop('checked', mainCheck);
            });
        });
    </script>
@endpush
