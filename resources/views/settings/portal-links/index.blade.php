@extends('layouts.app', ['title' => 'Kelola Portal Links'])

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-globe mr-1"></i> Data Portal Links
            </h6>
            <button class="btn btn-primary btn-sm" id="btnTambah">
                <i class="fas fa-plus mr-1"></i> Tambah Link
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="8%">Preview</th>
                            <th>Judul</th>
                            <th>URL</th>
                            <th width="10%">Kategori</th>
                            <th width="8%">Urutan</th>
                            <th width="8%">Status</th>
                            <th width="12%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Form --}}
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="formPortalLink" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="form_method" value="POST">
                    <input type="hidden" id="form_id" value="">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalTitle">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Portal Link
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">Judul <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" required
                                        placeholder="Contoh: SIMRS, E-Resep, Portal Karyawan">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="internal">Internal</option>
                                        <option value="external">External</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="url">URL Tujuan <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" id="url" name="url" required
                                placeholder="https://contoh.com/halaman">
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="2"
                                placeholder="Deskripsi singkat tentang link ini (opsional)"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="icon">Icon (FontAwesome)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconPreview">
                                                <i class="fas fa-link"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" id="icon" name="icon"
                                            placeholder="fas fa-server">
                                    </div>
                                    <small class="form-text text-muted">
                                        Cari icon di <a href="https://fontawesome.com/v5/search" target="_blank">FontAwesome</a>
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="order">Urutan</label>
                                    <input type="number" class="form-control" id="order" name="order" value="0" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="custom-control custom-switch mt-2">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                            value="1" checked>
                                        <label class="custom-control-label" for="is_active">Aktif</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="image">Gambar (Opsional)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="image" name="image"
                                    accept="image/*">
                                <label class="custom-file-label" for="image">Pilih gambar...</label>
                            </div>
                            <small class="form-text text-muted">Format: JPG, PNG, GIF, SVG, WebP. Max: 2MB. Jika diisi, gambar akan tampil menggantikan icon.</small>
                        </div>

                        <div id="imagePreviewContainer" class="text-center mt-2" style="display:none;">
                            <img id="imagePreviewImg" src="" class="img-thumbnail" style="max-height:120px;">
                            <br>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="btnRemoveImage">
                                <i class="fas fa-times mr-1"></i> Hapus Gambar
                            </button>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpan">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // DataTable
            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('settings.portal-link.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'preview', name: 'preview', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'title', name: 'title' },
                    { data: 'short_url', name: 'url' },
                    { data: 'category_badge', name: 'category', className: 'text-center' },
                    { data: 'order', name: 'order', className: 'text-center' },
                    { data: 'status_badge', name: 'is_active', className: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                }
            });

            // Reset form
            function resetForm() {
                $('#formPortalLink')[0].reset();
                $('#form_method').val('POST');
                $('#form_id').val('');
                $('#modalTitle').html('<i class="fas fa-plus-circle mr-1"></i> Tambah Portal Link');
                $('#imagePreviewContainer').hide();
                $('#imagePreviewImg').attr('src', '');
                $('#iconPreview i').attr('class', 'fas fa-link');
                $('.custom-file-label').text('Pilih gambar...');
                $('#is_active').prop('checked', true);
            }

            // Open modal for create
            $('#btnTambah').click(function() {
                resetForm();
                $('#modalForm').modal('show');
            });

            // Icon preview on input
            $('#icon').on('input', function() {
                var val = $(this).val().trim();
                if (val) {
                    $('#iconPreview i').attr('class', val);
                } else {
                    $('#iconPreview i').attr('class', 'fas fa-link');
                }
            });

            // Image file preview
            $('#image').on('change', function() {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreviewImg').attr('src', e.target.result);
                        $('#imagePreviewContainer').show();
                    };
                    reader.readAsDataURL(file);
                    $('.custom-file-label').text(file.name);
                }
            });

            // Remove image
            var removeImageFlag = false;
            $('#btnRemoveImage').click(function() {
                $('#image').val('');
                $('#imagePreviewContainer').hide();
                $('#imagePreviewImg').attr('src', '');
                $('.custom-file-label').text('Pilih gambar...');
                removeImageFlag = true;
            });

            // Submit form
            $('#formPortalLink').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var method = $('#form_method').val();
                var id = $('#form_id').val();
                var url = method === 'POST'
                    ? "{{ route('settings.portal-link.store') }}"
                    : "{{ url('settings/portal-links') }}/" + id;

                if (method === 'PUT') {
                    formData.append('_method', 'PUT');
                }

                if (removeImageFlag) {
                    formData.append('remove_image', '1');
                }

                // Handle is_active checkbox
                if (!$('#is_active').is(':checked')) {
                    formData.delete('is_active');
                }

                $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#modalForm').modal('hide');
                            table.ajax.reload();
                            resetForm();
                            removeImageFlag = false;
                            Swal.fire('Berhasil', res.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON?.errors;
                        if (errors) {
                            var msg = Object.values(errors).map(e => e[0]).join('<br>');
                            Swal.fire('Validasi Gagal', msg, 'error');
                        } else {
                            Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                        }
                    },
                    complete: function() {
                        $('#btnSimpan').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan');
                    }
                });
            });

            // Edit button
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                resetForm();
                removeImageFlag = false;

                $.get("{{ url('settings/portal-links') }}/" + id, function(res) {
                    if (res.success) {
                        var data = res.data;
                        $('#form_method').val('PUT');
                        $('#form_id').val(data.id);
                        $('#modalTitle').html('<i class="fas fa-edit mr-1"></i> Edit Portal Link');
                        $('#title').val(data.title);
                        $('#url').val(data.url);
                        $('#description').val(data.description);
                        $('#icon').val(data.icon);
                        $('#category').val(data.category);
                        $('#order').val(data.order);
                        $('#is_active').prop('checked', data.is_active);

                        if (data.icon) {
                            $('#iconPreview i').attr('class', data.icon);
                        }

                        if (data.image_url) {
                            $('#imagePreviewImg').attr('src', data.image_url);
                            $('#imagePreviewContainer').show();
                        }

                        $('#modalForm').modal('show');
                    }
                });
            });

            // Delete button
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Hapus Portal Link?',
                    text: 'Data yang sudah dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('settings/portal-links') }}/" + id,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                if (res.success) {
                                    table.ajax.reload();
                                    Swal.fire('Dihapus!', res.message, 'success');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Gagal menghapus data', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
