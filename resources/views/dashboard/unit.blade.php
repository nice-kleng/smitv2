<div class="container">
    <div class="row justify-content-center mb-5">
        <div class="col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jumlah Inventaris
                                Ruangan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['inventarisRuangan'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Permintaan Disetujui
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['permintaanAccepted'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Permintaan Dalam
                                Proses
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['permintaanDalamProses'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Permintaan Ditolak
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['permintaanRejected'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Permintaan Per Bulan Tahun {{ date('Y') }}
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="grafikPermintaan"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Data dari controller
            const grafikPermintaan = @json($data['grafikPermintaan']);

            // Persiapkan data untuk Chart.js
            const label = grafikPermintaan.map(item => item.bulan);
            const value = grafikPermintaan.map(item => item.total);

            // Buat grafik
            const chrtx = document.getElementById('grafikPermintaan').getContext('2d');
            new Chart(chrtx, {
                type: 'line',
                data: {
                    labels: label,
                    datasets: [{
                        label: 'Jumlah Permintaan',
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
        });
    </script>
@endpush
