@extends('layouts.app', ['title' => isset($menu) ? 'Edit Menu' : 'Tambah Menu'])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-bars"></i> {{ isset($menu) ? 'Edit Menu' : 'Tambah Menu' }}
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ isset($menu) ? route('settings.menu.update', $menu) : route('settings.menu.store') }}"
                    method="POST">
                    @csrf
                    @if(isset($menu))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nama Menu <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $menu->name ?? '') }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="icon">Icon (Font Awesome)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="{{ old('icon', $menu->icon ?? 'fas fa-icons') }}"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="icon" id="icon"
                                        class="form-control @error('icon') is-invalid @enderror"
                                        value="{{ old('icon', $menu->icon ?? '') }}"
                                        placeholder="fas fa-example">
                                    @error('icon')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    Lihat daftar icon di <a href="https://fontawesome.com/icons" target="_blank">Font Awesome</a>
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="route">Route <span class="text-danger">*</span></label>
                                <input type="text" name="route" id="route"
                                    class="form-control @error('route') is-invalid @enderror"
                                    value="{{ old('route', $menu->route ?? '') }}">
                                @error('route')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="module">Module <span class="text-danger">*</span></label>
                                <select name="module" id="module" class="form-control @error('module') is-invalid @enderror" required>
                                    <option value="">Pilih Module</option>
                                    @foreach($modules as $key => $module)
                                        <option value="{{ $key }}"
                                            {{ old('module', $menu->module ?? '') == $key ? 'selected' : '' }}>
                                            {{ ucfirst($key) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('module')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="permission_name">Permission</label>
                                <select name="permission_name" id="permission_name"
                                    class="form-control select2 @error('permission_name') is-invalid @enderror">
                                    <option value="">Public Menu</option>
                                    @foreach($permissions as $permission)
                                        <option value="{{ $permission->name }}"
                                            {{ old('permission_name', $menu->permission_name ?? '') == $permission->name ? 'selected' : '' }}>
                                            {{ $permission->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('permission_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="parent_id">Parent Menu</label>
                                <select name="parent_id" id="parent_id"
                                    class="form-control select2 @error('parent_id') is-invalid @enderror">
                                    <option value="">Menu Utama</option>
                                    @foreach($parentMenus as $parent)
                                        <option value="{{ $parent->id }}"
                                            {{ old('parent_id', $menu->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="order">Urutan <span class="text-danger">*</span></label>
                                <input type="number" name="order" id="order"
                                    class="form-control @error('order') is-invalid @enderror"
                                    value="{{ old('order', $menu->order ?? 0) }}" required>
                                @error('order')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active"
                                        name="is_active" value="1"
                                        {{ old('is_active', $menu->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Menu Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('settings.menu.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Preview icon
        $('#icon').on('input', function() {
            let icon = $(this).val() || 'fas fa-icons';
            $(this).closest('.input-group').find('.input-group-text i')
                .attr('class', icon);
        });
    });
</script>
@endpush
