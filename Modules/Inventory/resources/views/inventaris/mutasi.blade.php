@extends('inventory::layouts.master', ['title' => 'Mutasi Inventaris'])

@section('content')
    <div class="row mb-4" id="container-detail">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Riwayat Mutasi {{ $inventaris->kode_barang }} - {{ $inventaris->barang->nama_barang }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('inventory.store-mutasi') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="unit_id">Unit</label>
                                <input type="hidden" name="inventory_id" value="{{ $inventaris->id }}">
                                <select name="unit_id" id="unit_id" class="form-control" required>
                                    <option value="">-- Pilih Unit --</option>
                                    @foreach ($units as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama_unit }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="ruangan_id">Ruangan</label>
                                <select name="ruangan_id" id="ruangan_id" class="form-control" required disabled>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="kondisi">Kondisi</label>
                                <select name="kondisi" id="kondisi" class="form-control" required>
                                    <option value="2">Baik</option>
                                    <option value="1">Kurang Baik</option>
                                    <option value="0">Rusak</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="tanggal_mutasi">Tanggal Mutasi</label>
                                <input type="date" name="tanggal_mutasi" id="tanggal_mutasi" class="form-control"
                                    required>
                            </div>
                            <div class="col">
                                <label for="keterangan">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" rows="5" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col">
                                <button type="submit" class="btn btn-primary">Proses</button>
                                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>Unit</th>
                                    <th>Ruangan</th>
                                    <th>Kondisi</th>
                                    <th>Tanggal Mutasi</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventaris->historyMutasi as $history)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $history->unit->nama_unit }}</td>
                                        <td>{{ $history->ruangan->nama_ruangan }}</td>
                                        <td>
                                            @php
                                                $badge = [
                                                    '2' => 'badge-success',
                                                    '1' => 'badge-warning',
                                                    '0' => 'badge-danger',
                                                ];
                                            @endphp
                                            <span
                                                class="badge {{ $badge[$history->getRawOriginal('kondisi')] ?? 'badge-secondary' }}">{{ $history->kondisi }}</span>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($history->tanggal_mutasi)->locale('id_ID')->isoFormat('D MMMM Y') }}
                                        </td>
                                        <td>
                                            {{ $history->keterangan }}
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" class="btn btn-danger delete" title="Hapus Mutasi"
                                                data-id="{{ $history->id }}"><i class="fas fa-trash"></i></a>
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
            $('#unit_id').on('change', function() {
                $('#ruangan_id').empty();
                let id = $(this).val();

                $.ajax({
                    url: "{{ route('inventory.getRuangan') }}",
                    type: 'GET',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        $.each(response, function(i, val) {
                            $('#ruangan_id').append('<option value="' + val.id + '">' +
                                val.nama_ruangan +
                                '</option>');
                        });
                        $('#ruangan_id').prop('disabled', false);
                    }
                });
            });

            $('body').on('click', '.delete', function() {
                let id = $(this).data('id');

                if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                    $.ajax({
                        url: "{{ route('inventory.deleteMutasi', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                alert(response.message);
                                window.location.reload();
                            } else {
                                alert(response.message);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
