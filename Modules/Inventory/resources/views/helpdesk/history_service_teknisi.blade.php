@extends('inventory::layouts.master', ['title' => 'Riwayat Service Teknisi'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-stripped text-nowrap" id="serviceTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Ticket</th>
                                    <th>Tanggal Pengaduan</th>
                                    <th>Unit</th>
                                    <th>Ruangan</th>
                                    <th>Kode Inventaris</th>
                                    <th>Nama Barang</th>
                                    <th>Status</th>
                                    <th>Keterangan Perbaikan</th>
                                    <th>Tanggal Selesai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->kd_ticket }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->isoFormat('D MMMM Y HH:mm') }}</td>
                                        <td>{{ $item->ruangan->unit->nama_unit }}</td>
                                        <td>{{ $item->ruangan->nama_ruangan }}</td>
                                        <td>{{ $item->inventaris ? $item->inventaris->kode_barang : '-' }}</td>
                                        <td>{{ $item->inventaris ? $item->inventaris->barang->nama_barang : '-' }}</td>
                                        <td><span class="badge badge-success">Selesai</span></td>
                                        <td>
                                            <span class="badge badge-info">{{ $item->keterangan_perbaikan }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_perbaikan)->isoFormat('D MMMM Y HH:mm') }}
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#serviceTable').DataTable();
        });
    </script>
@endpush
