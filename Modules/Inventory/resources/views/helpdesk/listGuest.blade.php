<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpdesk RSI Jombang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
        }

        .main-content {
            min-height: 100vh;
            padding: 120px 0 40px;
            position: relative;
            z-index: 1;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 15px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 600;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand i {
            font-size: 1.4rem;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
        }

        .nav-btn {
            background: rgba(255, 255, 255, 0.9);
            color: #667eea;
            border-radius: 50px;
            padding: 8px 24px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-btn:hover {
            background: white;
            transform: translateY(-2px);
            color: #764ba2;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar.scrolled .navbar-brand {
            color: #667eea !important;
        }

        .container {
            position: relative;
            z-index: 2;
        }

        .card {
            background: white;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table-container {
            padding: 25px;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 8px 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 50px;
            padding: 5px 15px;
            margin: 0 3px;
            border: none;
            background: #f8fafc;
            color: #4a5568 !important;
            transition: all 0.3s ease;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #667eea !important;
            color: white !important;
            border: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            border: none;
            font-weight: 600;
        }

        .table {
            width: 100% !important;
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            border: none;
            padding: 15px;
            font-size: 0.95rem;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .badge {
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .badge-success {
            background: #10B981;
            color: white;
        }

        .badge-warning {
            background: #F59E0B;
            color: white;
        }

        .badge-info {
            background: #3B82F6;
            color: white;
        }

        .table-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 25px;
            border: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-card-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .refresh-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .refresh-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .table-card-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .refresh-btn {
                width: 100%;
                justify-content: center;
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center w-100">
                <a class="navbar-brand" href="#">
                    <i class="fas fa-headset"></i>
                    Helpdesk RSI Jombang
                </a>
                <div class="nav-buttons">
                    <a href="{{ route('helpdesk.index') }}" class="nav-btn text-decoration-none">
                        <i class="fas fa-home"></i>
                        Home
                    </a>
                    <a href="{{ route('login') }}" class="nav-btn text-decoration-none">
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card fade-in">
                        <div class="table-card-header">
                            <h1 class="table-card-title">
                                <i class="fas fa-ticket-alt"></i>
                                Daftar Tiket
                            </h1>
                            <button class="refresh-btn" onclick="refreshTable()">
                                <i class="fas fa-sync-alt"></i>
                                Refresh Data
                            </button>
                        </div>
                        <div class="table-container">
                            <div class="table-responsive">
                                <table class="table" id="ticketTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Aduan</th>
                                            <th>Tanggal Pengaduan</th>
                                            <th>Unit</th>
                                            <th>Ruangan</th>
                                            <th>Kerusakan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tickets as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->kd_ticket }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}
                                                </td>
                                                <td>{{ $item->ruangan->unit->nama_unit }}</td>
                                                <td>{{ $item->ruangan->nama_ruangan }}</td>
                                                <td>{{ $item->detail_aduan }}</td>
                                                <td>
                                                    @if ($item->status == 1)
                                                        <span class="badge badge-success">Selesai</span>
                                                    @elseif($item->status == 2)
                                                        <span class="badge badge-info">Diproses</span>
                                                    @else
                                                        <span class="badge badge-warning">Pending</span>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        @if (session('success'))
            Swal.fire({
                title: 'Success',
                text: '{{ session('success') }}',
                icon: 'success'
            });
        @endif

        let ticketTable;

        $(document).ready(function() {
            ticketTable = $('#ticketTable').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                order: [
                    [2, 'asc']
                ], // Sort by tanggal_pengaduan column descending
                columnDefs: [{
                    targets: 0,
                    width: '50px'
                }]
            });
        });

        function refreshTable() {
            location.reload();
        }

        // Add scroll event listener for navbar
        $(window).scroll(function() {
            if ($(window).scrollTop() > 50) {
                $('.navbar').addClass('scrolled');
            } else {
                $('.navbar').removeClass('scrolled');
            }
        });
    </script>
</body>

</html>
