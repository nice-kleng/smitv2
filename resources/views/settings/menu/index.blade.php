@extends('layouts.app', ['title' => 'Menu Management'])

@section('button-header')
<div class="btn-group">
    <a href="{{ route('settings.menu.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Menu
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-bars"></i> Menu Management
                </h5>
            </div>

            <div class="card-body">
                @foreach($menus->groupBy('module') as $module => $moduleMenus)
                <div class="module-section mb-4">
                    <h6 class="module-title">{{ strtoupper($module) }}</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-nowrap">
                            <thead>
                                <tr>
                                    <th style="width: 10px"></th>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Icon</th>
                                    <th>Route</th>
                                    <th>Permission</th>
                                    <th>Parent</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="sortable" data-module="{{ $module }}">
                                @foreach($moduleMenus->sortBy('order') as $menu)
                                <tr data-id="{{ $menu->id }}">
                                    <td><i class="fas fa-grip-vertical handle" style="cursor: move"></i></td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $menu->name }}</td>
                                    <td><i class="{{ $menu->icon }}"></i> {{ $menu->icon }}</td>
                                    <td>{{ $menu->route }}</td>
                                    <td>
                                        @if($menu->permission)
                                        <span class="badge badge-info">{{ $menu->permission->name }}</span>
                                        @else
                                        <span class="badge badge-secondary">Public</span>
                                        @endif
                                    </td>
                                    <td>{{ $menu->parent->name ?? '-' }}</td>
                                    <td>{{ $menu->order }}</td>
                                    <td>
                                        <span class="badge {{ $menu->is_active ? 'badge-success' : 'badge-danger' }}">
                                            {{ $menu->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('settings.menu.edit', $menu) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('settings.menu.destroy', $menu) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                {{-- Tampilkan child menu --}}
                                @foreach($menu->children->sortBy('order') as $child)
                                <tr data-id="{{ $child->id }}" class="child-row">
                                    <td><i class="fas fa-grip-vertical handle" style="cursor: move"></i></td>
                                    <td>{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                                    <td><span class="pl-3">└─ {{ $child->name }}</span></td>
                                    <td><i class="{{ $child->icon }}"></i> {{ $child->icon }}</td>
                                    <td>{{ $child->route }}</td>
                                    <td>
                                        @if($child->permission)
                                        <span class="badge badge-info">{{ $child->permission->name }}</span>
                                        @else
                                        <span class="badge badge-secondary">Public</span>
                                        @endif
                                    </td>
                                    <td>{{ $child->parent->name }}</td>
                                    <td>{{ $child->order }}</td>
                                    <td>
                                        <span class="badge {{ $child->is_active ? 'badge-success' : 'badge-danger' }}">
                                            {{ $child->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('settings.menu.edit', $child) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('settings.menu.destroy', $child) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .sortable tr {
        cursor: move;
    }

    .ui-sortable-helper {
        display: table;
        background: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .handle:hover {
        color: #666;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(document).ready(function () {
        $('.sortable').sortable({
            handle: '.handle',
            axis: 'y',
            helper: function(e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function(index) {
                    $(this).width($originals.eq(index).width());
                });
                return $helper;
            },
            update: function(event, ui) {
                let orders = [];
                let module = $(this).data('module');

                $(this).find('tr').each(function(index) {
                    orders.push({
                        id: $(this).data('id'),
                        order: index + 1,
                        is_parent: !$(this).hasClass('child-row')
                    });
                });

                $.ajax({
                    url: '{{ route("settings.menu.update-order") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        module: module,
                        orders: orders
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Menu order updated successfully',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr.responseJSON);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to update menu order'
                        });
                    }
                });
            }
        });
    });
</script>
@endpush
