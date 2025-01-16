@extends('inventory::layouts.master', ['title' => 'Rekap Service (Luar) Inventaris'])


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
                                    <th>Kode Inventaris</th>
                                    <th>Nama Barang</th>
                                    <th>Tempat Service</th>
                                    <th>Kerusakan</th>
                                    <th>Biaya</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->inventory->kode_barang }}</td>
                                        <td>{{ $item->inventory->barang->nama_barang }}</td>
                                        <td>{{ $item->tempat_service }}</td>
                                        <td>{{ $item->Kerusakan }}</td>
                                        <td>@currency($item->biaya)</td>
                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->isoFormat('D MMMM Y') }}</td>
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
