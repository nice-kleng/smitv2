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

<!-- Tambahkan Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>Rusak</td>
                                <td>Kurang Baik</td>
                                <td>Baik</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $data['totalInventarisRusak'] }}</td>
                                <td>{{ $data['totalInventarisKurangBaik'] }}</td>
                                <td>{{ $data['totalInventarisBaik'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>








<script>
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
</script>
