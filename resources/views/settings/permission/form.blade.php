@extends('layouts.app', ['title' => isset($permission) ? 'Edit Permission' : 'Tambah Permission'])

@section('content')
<div class="row">
    <div class="col-md-12">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-key"></i> {{ isset($permission) ? 'Edit Permission' : 'Tambah Permission' }}
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ isset($permission) ? route('settings.permission.update', $permission) : route('settings.permission.store') }}"
                    method="POST">
                    @csrf
                    @if(isset($permission))
                        @method('PUT')
                    @endif

                    <div class="form-group">
                        <label>Nama Permission</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $permission->name ?? '') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Modules</label>
                        <select name="modules" class="form-control @error('modules') is-invalid @enderror" required>
                            <option value="">Pilih Modules</option>
                            @foreach($modules as $module)
                                <option value="{{ $module }}"
                                    {{ old('modules', $permission->modules ?? '') == $module ? 'selected' : '' }}>
                                    {{ ucfirst($module) }}
                                </option>
                            @endforeach
                        </select>
                        @error('modules')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="group_name">Group (Controller)</label>
                        <input type="text" name="group_name" class="form-control @error('group_name') is-invalid @enderror"
                            value="{{ old('group_name', $permission->group_name ?? '') }}" required>
                        @error('group_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                            rows="3">{{ old('description', $permission->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('settings.permission.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
