@extends('layouts.app', ['title' => 'User Management'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">
                        <i class="fas fa-users"></i> User Management
                    </h5>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createUserModal">
                        <i class="fas fa-plus"></i> Tambah User
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="userTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Ruangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->roles->pluck('name')->implode(', ') }}</td>
                                        <td>{{ $item->ruangan->nama_ruangan ?? '-' }}</td>
                                        <td class="d-flex gap-3 justify-content-end">
                                            <button type="button" class="btn btn-sm btn-info"
                                                onclick="editUser({{ $item->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning"
                                                onclick="manageRoles({{ $item->id }})">
                                                <i class="fas fa-user-tag"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="deleteUser({{ $item->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
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

    <!-- Create User Modal -->
    <div class="modal fade" role="dialog" id="createUserModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('settings.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unit</label>
                            <select name="unit_id" class="form-control select2">
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="form-label">Ruangan</label>
                            <select name="ruangan_id" id="ruangan_id" class="form-control" required></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="roles[]" class="form-control select2" multiple>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Kosongkan jika tidak ingin mengubah password">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unit</label>
                            <select name="unit_id" class="form-control select2">
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="form-label">Ruangan</label>
                            <select name="ruangan_id" id="ruangan_id" class="form-control" required></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="roles[]" class="form-control select2" multiple>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Manage Roles Modal -->
    <div class="modal fade" id="manageRolesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="manageRolesForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Manage User Roles</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Roles</label>
                            <div class="role-checkboxes">
                                @foreach ($roles as $role)
                                    <div class="form-check">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                            class="form-check-input" id="role{{ $role->id }}">
                                        <label class="form-check-label" for="role{{ $role->id }}">
                                            {{ $role->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function editUser(id) {
            $.ajax({
                url: `{{ url('settings/users') }}/${id}`,
                type: 'GET',
                success: function(data) {
                    console.log(data);

                    const form = $('#editUserModal form');
                    form.attr('action', `{{ url('settings/users') }}/${id}`);
                    form.find('input[name="name"]').val(data.name);
                    form.find('input[name="email"]').val(data.email);
                    form.find('select[name="unit_id"]').val(data.unit_id).trigger('change');
                    // Reset password field karena ini optional saat edit
                    form.find('input[name="password"]').val('').removeAttr('required');
                    form.find('select[name="ruangan_id"]').val(data.ruangan_id).trigger('change');

                    // Set selected roles dan trigger select2
                    const roleSelect = form.find('select[name="roles[]"]');
                    roleSelect.val(data.roles.map(role => role.id)).trigger('change');

                    $('#editUserModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil data user');
                }
            });
        }

        function manageRoles(id) {
            $.get(`{{ route('settings.users.roles', ':id') }}`.replace(':id', id))
                .done(function(data) {
                    // Uncheck all role checkboxes
                    $('input[name="roles[]"]').prop('checked', false);

                    // Check user's roles
                    $.each(data.roles, function(i, roleId) {
                        $(`#role${roleId}`).prop('checked', true);
                    });

                    // Set form action
                    $('#manageRolesForm').attr('action', `{{ route('settings.users.roles', ':id') }}`.replace(':id',
                        id));

                    $('#manageRolesModal').modal('show');
                });
        }

        function deleteUser(id) {
            if (confirm('Apakah anda yakin ingin menghapus user ini?')) {
                $.ajax({
                    url: `{{ route('settings.users.destroy', ':id') }}`.replace(':id', id),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        location.reload();
                    }
                });
            }
        }

        $(document).ready(function() {
            $('#userTable').DataTable();

            $('#unit_id').change(function(e) {
                e.preventDefault();
                let id = $(this).val();

                $.ajax({
                    type: "get",
                    url: `{{ route('master.unit.getRuangan', ':id') }}`.replace(':id', id),
                    dataType: "json",
                    success: function(response) {
                        console.log(response);
                    }
                });
            });
        });
    </script>
@endpush
