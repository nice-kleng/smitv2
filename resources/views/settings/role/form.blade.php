@extends('layouts.app', ['title' => isset($role) ? 'Edit Role' : 'Tambah Role'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-user-shield"></i> {{ isset($role) ? 'Edit Role' : 'Tambah Role' }}
                    </h5>
                </div>

                <div class="card-body">
                    <form action="{{ isset($role) ? route('settings.role.update', $role) : route('settings.role.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($role))
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label>Nama Role</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $role->name ?? '') }}" required
                                {{ isset($role) && $role->name === 'superadmin' ? 'readonly' : '' }}>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Permissions</label>
                            <div class="row">
                                @foreach ($permissions->groupBy('group') as $group => $groupedPermissions)
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">{{ ucfirst($group) }}</h6>
                                            </div>
                                            <div class="card-body">
                                                @foreach ($groupedPermissions as $permission)
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="perm{{ $permission->id }}" name="permissions[]"
                                                            value="{{ $permission->name }}"
                                                            @if (isset($role) && $role->hasPermissionTo($permission->name)) checked @endif
                                                            @if (isset($role) && $role->name === 'superadmin') disabled @endif>
                                                        <label class="custom-control-label" for="perm{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('settings.role.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
