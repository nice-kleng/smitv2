<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
        }

        .error-page {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .error-content {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
        }

        .error-code {
            font-size: 3.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .error-message {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .btn-back {
            background: #2c3e50;
            border: none;
            padding: 0.8rem 2rem;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #34495e;
            transform: translateY(-2px);
        }

        /* Medical themed decorative elements */
        .medical-icon {
            position: absolute;
            opacity: 0.1;
            color: #2c3e50;
        }

        .icon-1 {
            top: 10%;
            left: 10%;
            font-size: 3rem;
        }

        .icon-2 {
            top: 20%;
            right: 15%;
            font-size: 2.5rem;
        }

        .icon-3 {
            bottom: 15%;
            left: 20%;
            font-size: 2rem;
        }

        .icon-4 {
            bottom: 20%;
            right: 10%;
            font-size: 3.5rem;
        }
    </style>
</head>

<body>
    <div class="error-page">
        <!-- Decorative medical icons -->
        <i class="fas fa-heartbeat medical-icon icon-1"></i>
        <i class="fas fa-stethoscope medical-icon icon-2"></i>
        <i class="fas fa-pills medical-icon icon-3"></i>
        <i class="fas fa-hospital medical-icon icon-4"></i>

        <div class="error-content">
            <div class="error-icon">
                <i class="fas fa-file-medical"></i>
            </div>
            <h1 class="error-code">404</h1>
            <h2 class="h4 mb-3">Halaman Tidak Ditemukan</h2>
            <p class="error-message">
                Mohon maaf, halaman yang Anda cari tidak dapat ditemukan.<br>
                Mungkin halaman telah dipindahkan atau dihapus.
            </p>
            <a href="{{ url('/') }}" class="btn btn-primary btn-back">
                <i class="fas fa-home me-2"></i>Kembali ke Beranda
            </a>
        </div>
    </div>
</body>

</html>
