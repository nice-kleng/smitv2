@extends('layouts.app', ['title' => 'Profile'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">

                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Profile Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user"></i> Profil Saya
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Profile Avatar -->
                            <div class="col-md-4 text-center mb-4">
                                <div class="profile-avatar">
                                    <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                                        style="width: 120px; height: 120px; border-radius: 50%; font-size: 48px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <h5 class="text-primary">{{ $user->name }}</h5>
                                    <p class="text-muted">{{ $user->email }}</p>
                                </div>
                            </div>

                            <!-- Profile Information -->
                            <div class="col-md-8">
                                <div class="profile-info">
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong><i class="fas fa-user text-muted"></i> Nama Lengkap:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $user->name }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong><i class="fas fa-at text-muted"></i> Username:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $user->username }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong><i class="fas fa-envelope text-muted"></i> Email:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $user->email }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong><i class="fas fa-building text-muted"></i> Unit:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $user->unit ? $user->unit->name : '-' }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong><i class="fas fa-door-open text-muted"></i> Ruangan:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $user->ruangan ? $user->ruangan->name : '-' }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong><i class="fas fa-code text-muted"></i> PU Code:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            <span class="badge badge-info">{{ $user->pu_kd_label }}</span>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong><i class="fas fa-calendar text-muted"></i> Bergabung:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $user->created_at->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Profil
                                </a>
                                <a href="{{ route('profile.change-password') }}" class="btn btn-warning">
                                    <i class="fas fa-key"></i> Ubah Password
                                </a>
                            </div>
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> Terakhir diupdate:
                                    {{ $user->updated_at->format('d M Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information Card -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle"></i> Informasi Tambahan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <h6 class="text-primary">
                                        <i class="fas fa-shield-alt"></i> Role & Permissions
                                    </h6>
                                    @if ($user->roles->count() > 0)
                                        @foreach ($user->roles as $role)
                                            <span class="badge badge-success mr-1">{{ $role->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Tidak ada role yang ditetapkan</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <h6 class="text-primary">
                                        <i class="fas fa-book"></i> Total Log Book
                                    </h6>
                                    <span class="badge badge-primary">{{ $user->logBook->count() }} entries</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .profile-info .row {
            border-bottom: 1px solid #f0f0f0;
            padding: 10px 0;
        }

        .profile-info .row:last-child {
            border-bottom: none;
        }

        .avatar-circle {
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .info-item h6 {
            margin-bottom: 8px;
        }
    </style>
@endsection
