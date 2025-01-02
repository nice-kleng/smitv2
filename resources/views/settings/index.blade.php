@extends('layouts.app', ['title' => 'Settings'])

@section('content')
    <div class="row">
        <!-- Menu Management Card -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-bars"></i> Menu Management
                    </h5>
                    <p class="card-text">Kelola menu sistem dan hak aksesnya</p>
                    <a href="{{ route('settings.menu.index') }}" class="btn btn-primary">
                        Kelola Menu
                    </a>
                </div>
            </div>
        </div>

        <!-- User Management Card -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-users"></i> User Management
                    </h5>
                    <p class="card-text">Kelola pengguna dan hak aksesnya</p>
                    <a href="{{ route('settings.users.index') }}" class="btn btn-primary">
                        Kelola User
                    </a>
                </div>
            </div>
        </div>

        <!-- Role Management Card -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-user-tag"></i> Role Management
                    </h5>
                    <p class="card-text">Kelola role dan permissionnya</p>
                    <a href="{{ route('settings.role.index') }}" class="btn btn-primary">
                        Kelola Role
                    </a>
                </div>
            </div>
        </div>

        <!-- Permission Management Card -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-key"></i> Permission Management
                    </h5>
                    <p class="card-text">Kelola permission sistem</p>
                    <a href="{{ route('settings.permission.index') }}" class="btn btn-primary">
                        Kelola Permission
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
