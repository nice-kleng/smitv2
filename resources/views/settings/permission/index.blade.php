@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Permission Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('settings.permission.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Permission
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Form Generate Permission --}}
                    <div class="mb-4">
                        <form action="{{ route('settings.permission.generate') }}" method="POST" class="form-inline">
                            @csrf
                            <select name="module" class="form-control mr-2" required>
                                <option value="">Pilih Module</option>
                                @foreach(['base', 'inventory', 'helpdesk'] as $module)
                                    <option value="{{ $module }}">{{ ucfirst($module) }}</option>
                                @endforeach
                            </select>
                            <div class="form-check form-check-inline mr-2">
                                @foreach(['view', 'create', 'edit', 'delete', 'manage'] as $action)
                                    <label class="mr-2">
                                        <input type="checkbox" name="actions[]" value="{{ $action }}" class="mr-1">
                                        {{ ucfirst($action) }}
                                    </label>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-info">Generate Permission</button>
                        </form>
                    </div>

                    {{-- Permission List --}}
                    @foreach($permissions as $group => $groupPermissions)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">{{ ucfirst($group) }}</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th width="15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($groupPermissions as $permission)
                                                <tr>
                                                    <td>{{ $permission->name }}</td>
                                                    <td>{{ $permission->description }}</td>
                                                    <td>
                                                        <a href="{{ route('settings.permission.edit', $permission) }}"
                                                           class="btn btn-info btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('settings.permission.destroy', $permission) }}"
                                                              method="POST"
                                                              class="d-inline"
                                                              onsubmit="return confirm('Are you sure?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
