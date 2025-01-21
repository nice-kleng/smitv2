@extends('layouts.app', ['title' => 'User Management'])

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users"></i> User Management
                    </h5>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createUserModal">
                        <i class="fas fa-plus"></i> Tambah User
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-nowrap" id="userTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Unit</th>
                                    <th>Ruangan</th>
                                    <th>PU</th>
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
                                        <td>{{ $item->unit->nama_unit ?? '-' }}</td>
                                        <td>{{ $item->ruangan->nama_ruangan ?? '-' }}</td>
                                        <td>{{ $item->pu_kd_label }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info"
                                                    onclick="editUser({{ $item->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning"
                                                    onclick="manageRoles({{ $item->id }})">
                                                    <i class="fas fa-user-tag"></i>
                                                </button>
                                                @role('superadmin')
                                                    <a href="{{ route('settings.users.permissions', $item->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-key"></i>
                                                    </a>
                                                @endrole
                                                @if (!$item->hasRole('superadmin'))
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="deleteUser({{ $item->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
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

    @include('settings.users.modals.create')
    @include('settings.users.modals.edit')
    @include('settings.users.modals.roles')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#userTable').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                }
            });

            // Initialize Select2
            $('.select2').select2({
                width: '100%',
                // dropdownParent: $('.modal')
            });

            // Handle unit change for ruangan dropdown in create modal
            $('#create_unit_id').on('change', function() {
                loadRuangan($(this).val(), '#create_ruangan_id');
            });

            // Handle unit change for ruangan dropdown in edit modal
            $('#edit_unit_id').on('change', function() {
                loadRuangan($(this).val(), '#edit_ruangan_id');
            });

            // Reset form when modal is closed
            $('.modal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('select').val(null).trigger('change');
            });
        });

        function loadRuangan(unitId, targetSelect) {
            const ruanganSelect = $(targetSelect);

            if (!unitId) {
                ruanganSelect.html('<option value="">Pilih Ruangan</option>');
                return;
            }

            $.ajax({
                url: `{{ route('api.master.unit.ruangan', ':id') }}`.replace(':id', unitId),
                type: 'GET',
                beforeSend: function() {
                    ruanganSelect.html('<option value="">Loading...</option>');
                },
                success: function(response) {
                    let options = '<option value="">Pilih Ruangan</option>';
                    response.forEach(function(item) {
                        options += `<option value="${item.id}">${item.nama_ruangan}</option>`;
                    });
                    ruanganSelect.html(options);
                },
                error: function() {
                    ruanganSelect.html('<option value="">Error loading data</option>');
                    toastr.error('Gagal memuat data ruangan');
                }
            });
        }

        function editUser(id) {
            $.ajax({
                url: `{{ url('settings/users') }}/${id}/edit`,
                type: 'GET',
                beforeSend: function() {
                    $('#editUserModal form')[0].reset();
                    $('#editUserModal select').val(null).trigger('change');
                },
                success: function(data) {
                    const form = $('#editUserModal form');
                    form.attr('action', `{{ url('settings/users') }}/${id}`);

                    // Set form values
                    form.find('input[name="name"]').val(data.name);
                    form.find('input[name="email"]').val(data.email);
                    form.find('#edit_unit_id').val(data.unit_id).trigger('change');
                    form.find('select[name="pu_kd"]').val(data.pu_kd).trigger('change');

                    // Set ruangan after unit is loaded
                    setTimeout(() => {
                        form.find('#edit_ruangan_id').val(data.ruangan_id).trigger('change');
                    }, 1000);

                    // Set roles
                    const roleSelect = form.find('select[name="roles[]"]');
                    const roleNames = data.roles.map(role => role.name);
                    roleSelect.val(roleNames).trigger('change');

                    $('#editUserModal').modal('show');
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat mengambil data user';
                    toastr.error(message);
                }
            });
        }

        function manageRoles(id) {
            $.ajax({
                url: `{{ route('settings.users.roles', ':id') }}`.replace(':id', id),
                type: 'GET',
                beforeSend: function() {
                    $('input[name="roles[]"]').prop('checked', false);
                },
                success: function(data) {
                    // Check user's roles
                    data.roles.forEach(function(roleId) {
                        $(`#role${roleId}`).prop('checked', true);
                    });

                    // Set form action
                    $('#manageRolesForm').attr('action',
                        `{{ route('settings.users.roles', ':id') }}`.replace(':id', id)
                    );

                    $('#manageRolesModal').modal('show');
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat mengambil data roles';
                    toastr.error(message);
                }
            });
        }

        function deleteUser(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah anda yakin ingin menghapus user ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('settings.users.destroy', ':id') }}`.replace(':id', id),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            toastr.success('User berhasil dihapus');
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        },
                        error: function(xhr) {
                            const message = xhr.responseJSON?.message ||
                                'Terjadi kesalahan saat menghapus user';
                            toastr.error(message);
                        }
                    });
                }
            });
        }
    </script>
@endpush
