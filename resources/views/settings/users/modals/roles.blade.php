<!-- Manage Roles Modal -->
<div class="modal fade" id="manageRolesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="manageRolesForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Kelola Role User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @error('roles')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="form-group">
                        <label class="form-label required">Roles</label>
                        <div class="role-checkboxes">
                            @foreach ($roles as $role)
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                        class="custom-control-input" id="role{{ $role->id }}">
                                    <label class="custom-control-label" for="role{{ $role->id }}">
                                        {{ $role->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .role-checkboxes {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
    </style>
@endpush
