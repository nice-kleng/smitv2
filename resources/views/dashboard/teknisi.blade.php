<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pengaduan Baru</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['newTickets'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Tickets
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['completedTickets'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Kategori Inentaris dengan Kerusakan Terbanyak</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Total Kerusakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['kategoriSeringRusak'] as $kategori)
                                <tr>
                                    <td>{{ $kategori->nama_kategori }}</td>
                                    <td>{{ $kategori->total_kerusakan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Ruangan dengan Kerusakan Terbanyak</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Ruangan</th>
                                <th>Total Kerusakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['ruanganSeringRusak'] as $ruangan)
                                <tr>
                                    <td>{{ $ruangan->nama_unit }} - {{ $ruangan->nama_ruangan }}</td>
                                    <td>{{ $ruangan->total_kerusakan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Canvas untuk grafik -->
<div class="row justify-content-center mb-5">
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Grafik Kerusakan Per Bulan {{ date('Y') }}</h6>
            </div>
            <div class="card-body">
                <canvas id="grafikKerusakan"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Jumlah Total Inventaris IT</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" style="margin-bottom: 50px;">
                        <thead>
                            <tr>
                                <td>Rusak</td>
                                <td>Kurang Baik</td>
                                <td>Baik</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><button class="btn btn-info btn-sm">{{ $data['totalInventarisRusak'] }}</button></i>
                                </td>
                                <td><button
                                        class="btn btn-info btn-sm">{{ $data['totalInventarisKurangBaik'] }}</button>
                                </td>
                                <td><button class="btn btn-info btn-sm">{{ $data['totalInventarisBaik'] }}</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="detailKondisiByKategori" style="width: 100%;">
                        <thead>
                            <tr class="text-center">
                                <th>Kategori Barang</th>
                                <th>Rusak</th>
                                <th>Kurang Baik</th>
                                <th>Baik</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Inventaris -->
<div class="modal fade" id="detailInventarisModal" tabindex="-1" role="dialog"
    aria-labelledby="detailInventarisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailInventarisModalLabel">Detail Inventaris</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="tableDetailInventaris">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>No Barang</th>
                                <th>Merk</th>
                                <th>Type</th>
                                <th>Unit</th>
                                <th>Ruangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Data dari controller
            const grafikKerusakan = @json($data['grafikKerusakan']);

            // Persiapkan data untuk Chart.js
            const label = grafikKerusakan.map(item => item.bulan);
            const value = grafikKerusakan.map(item => item.total);

            // Buat grafik
            const chrtx = document.getElementById('grafikKerusakan').getContext('2d');
            new Chart(chrtx, {
                type: 'bar',
                data: {
                    labels: label,
                    datasets: [{
                        label: 'Jumlah Kerusakan',
                        data: value,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Initialize DataTable for detailKondisiByKategori
            $('#detailKondisiByKategori').DataTable({
                data: @json($data['detailKondisiByKategori']),
                columns: [{
                        data: 'nama_kategori'
                    },
                    {
                        data: 'rusak',
                        render: function(data, type, row) {
                            return `<button class="btn btn-danger btn-sm detail-inventaris"
                                data-kategori="${row.nama_kategori}"
                                data-kondisi="0">${data}</button>`;
                        }
                    },
                    {
                        data: 'kurang_baik',
                        render: function(data, type, row) {
                            return `<button class="btn btn-warning btn-sm detail-inventaris"
                                data-kategori="${row.nama_kategori}"
                                data-kondisi="1">${data}</button>`;
                        }
                    },
                    {
                        data: 'baik',
                        render: function(data, type, row) {
                            return `<button class="btn btn-success btn-sm detail-inventaris"
                                data-kategori="${row.nama_kategori}"
                                data-kondisi="2">${data}</button>`;
                        }
                    }
                ],
                pageLength: 5,
                ordering: true,
                searching: false,
                info: false,
                paging: true,
                lengthChange: false,
                border: true,
            });

            // Event handler untuk tombol detail
            $('#detailKondisiByKategori').on('click', '.detail-inventaris', function() {
                const kategori = $(this).data('kategori');
                const kondisi = $(this).data('kondisi');

                // Destroy existing DataTable if exists
                if ($.fn.DataTable.isDataTable('#tableDetailInventaris')) {
                    $('#tableDetailInventaris').DataTable().destroy();
                }

                // Initialize DataTable for modal
                $('#tableDetailInventaris').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('dashboard.detail-inventaris') }}',
                        data: {
                            kategori: kategori,
                            kondisi: kondisi
                        }
                    },
                    columns: [{
                            data: 'kode_barang'
                        },
                        {
                            data: 'no_barang'
                        },
                        {
                            data: 'merk'
                        },
                        {
                            data: 'type'
                        },
                        {
                            data: 'unit'
                        },
                        {
                            data: 'ruangan'
                        }
                    ],
                    pageLength: 5,
                    ordering: true,
                    searching: true,
                    info: true,
                });

                $('#detailInventarisModal').modal('show');
            });
        });
    </script>
@endpush
