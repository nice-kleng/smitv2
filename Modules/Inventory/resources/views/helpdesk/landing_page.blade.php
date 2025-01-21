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
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            border: none;
        }

        .card-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .form-label {
            font-weight: 500;
            color: #4a5568;
            margin-bottom: 8px;
        }

        .form-select,
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .input-group-text {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-left: none;
            color: #4a5568;
        }

        textarea {
            min-height: 120px;
            resize: none;
        }

        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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
                    <a href="{{ route('helpdesk.antrean') }}" class="nav-btn text-decoration-none">
                        <i class="fas fa-list-ol"></i>
                        On Progress
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
                <div class="col-md-6">
                    <div class="card fade-in">
                        <div class="card-header text-center">
                            <h1 class="card-title">
                                <i class="fas fa-tools me-2"></i>
                                Form Laporan Kerusakan
                            </h1>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('helpdesk.store-ticket') }}" method="post">
                                @csrf
                                <div class="mb-3">
                                    <label for="ruangan_id" class="form-label">Unit</label>
                                    <div class="input-group">
                                        <select id="ruangan_id" name="ruangan_id" class="form-select" required>
                                            <option value="" disabled selected>Pilih Unit</option>
                                            @foreach ($ruangans as $ruangan)
                                                <option value="{{ $ruangan->id }}">
                                                    {{ Str::ucfirst($ruangan->unit->nama_unit) }} -
                                                    {{ Str::ucfirst($ruangan->nama_ruangan) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    </div>
                                </div>

                                {{-- <div class="mb-3">
                                    <label for="ruangan_id" class="form-label">Ruangan</label>
                                    <div class="input-group">
                                        <select id="ruangan_id" name="ruangan_id" class="form-select" required>
                                            <option value="" disabled selected>Pilih Ruangan</option>
                                        </select>
                                        <span class="input-group-text"><i class="fas fa-door-open"></i></span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="inventaris_id" class="form-label">Inventaris</label>
                                    <div class="input-group">
                                        <select id="inventaris_id" name="inventaris_id" class="form-select" required>
                                            <option value="" disabled selected>Pilih Inventaris</option>
                                        </select>
                                        <span class="input-group-text"><i class="fas fa-box"></i></span>
                                    </div>
                                </div> --}}

                                <div class="mb-4">
                                    <label for="detail_aduan" class="form-label">Deskripsi Kerusakan</label>
                                    <div class="input-group">
                                        <textarea id="detail_aduan" name="detail_aduan" class="form-control"
                                            placeholder="Jelaskan detail kerusakan yang terjadi..." required></textarea>
                                        <span class="input-group-text align-items-start pt-2">
                                            <i class="fas fa-pencil-alt"></i>
                                        </span>
                                    </div>
                                </div>

                                <button type="submit" class="submit-btn w-100">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Kirim Laporan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Add scroll event listener for navbar
        $(window).scroll(function() {
            if ($(window).scrollTop() > 50) {
                $('.navbar').addClass('scrolled');
            } else {
                $('.navbar').removeClass('scrolled');
            }
        });

        // @if (session('success'))
        //     Swal.fire({
        //         title: 'Success',
        //         text: '{{ session('success') }}',
        //         icon: 'success'
        //     });
        // @endif

        $(document).ready(function() {
            // $('#unit_id').on('change', function() {
            //     let unitId = $(this).val();
            //     if (unitId) {
            //         $('#ruangan_id').empty();
            //         $('#inventaris_id').empty();
            //         $('#ruangan_id').append('<option value="" selected disabled>Pilih Ruangan</option>');
            //         $('#inventaris_id').append(
            //             '<option value="" selected disabled>Pilih Inventaris</option>');

            //         $.get('/helpdesk/getRuanganByUnit', {
            //             unit_id: unitId
            //         }, function(data) {
            //             if (data) {
            //                 $.each(data, function(key, value) {
            //                     $('#ruangan_id').append('<option value="' + value.id +
            //                         '">' + value.nama_ruangan + '</option>');
            //                 });
            //             }
            //         });
            //     }
            // });

            // $('#ruangan_id').on('change', function() {
            //     let ruanganId = $(this).val();
            //     console.log(ruanganId);

            //     if (ruanganId) {
            //         $('#inventaris_id').empty();
            //         $('#inventaris_id').append(
            //             '<option value="" selected disabled>Pilih Inventaris</option>');

            //         $.ajax({
            //             url: '/helpdesk/getInventarisByRuangan',
            //             type: 'GET',
            //             data: {
            //                 ruangan_id: ruanganId
            //             },
            //             success: function(data) {
            //                 if (data && data.length > 0) {
            //                     $.each(data, function(key, value) {
            //                         let namaBarang = value.master_barang ?
            //                             value.master_barang.nama_barang + ' - ' + value
            //                             .kode_inventori :
            //                             value.kode_inventori;
            //                         $('#inventaris_id').append('<option value="' + value
            //                             .id + '">' + namaBarang + '</option>');
            //                     });
            //                 } else {
            //                     $('#inventaris_id').append(
            //                         '<option value="" disabled>Tidak ada inventaris di ruangan ini</option>'
            //                     );
            //                 }
            //             },
            //             error: function(xhr, status, error) {
            //                 console.error('Error:', error);
            //                 Swal.fire({
            //                     title: 'Error!',
            //                     text: 'Gagal mengambil data inventaris: ' + error,
            //                     icon: 'error'
            //                 });
            //             }
            //         });
            //     }
            // });

            $('form').on('submit', function(e) {
                e.preventDefault();

                if (!$('#ruangan_id').val() || !$('#detail_aduan').val()) {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Semua field harus diisi',
                        icon: 'warning'
                    });
                    return false;
                }

                this.submit();
            });
        });
    </script>
</body>

</html>
