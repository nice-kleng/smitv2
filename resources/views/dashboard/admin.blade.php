<div class="row justify-content-center">
    <div class="col-md-3 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Permintaan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['totalPermintaan'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Permintaan Baru</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['totalPermintaanBaru'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tag fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Permintaan Menunggu
                            Diambil</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $data['totalPermintaanMenungguDiambil'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan Chart.js jika belum ada -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Canvas untuk grafik -->
<div class="row">
    <div class="col-md-6">
        <div class="card shadow mb-3">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Grafik Permintaan Per Bulan {{ date('Y') }}</h6>
            </div>
            <div class="card-body">
                <canvas id="grafikPermintaan"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow mb-3">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Unit dan Ruangan Dengan Permintaan Terbanyak Tahun
                    {{ date('Y') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tblPemintaTerbanyak">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Unit</th>
                                <th>Ruangan</th>
                                <th>Total Permintaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data['unitPermintaanTerbanyak'] as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->unit->nama_unit }}</td>
                                    <td>{{ $item->ruangan->nama_ruangan }}</td>
                                    <td>{{ $item->total_permintaan }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Data dari controller
    const grafikData = @json($data['grafikPermintaan']);

    // Persiapkan data untuk Chart.js
    const labels = grafikData.map(item => item.bulan);
    const values = grafikData.map(item => item.total);

    // Buat grafik
    const ctx = document.getElementById('grafikPermintaan').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Permintaan',
                data: values,
                backgroundColor: 'rgba(78, 115, 223, 0.5)',
                borderColor: 'rgba(78, 115, 223, 1)',
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
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Jumlah Permintaan: ${context.raw}`;
                        }
                    }
                }
            }
        }
    });
</script>
