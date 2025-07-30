<!-- Create Schedule Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Quick Create Jadwal
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickCreateForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="quickStartDate" class="form-label">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="quickStartDate" name="start_date"
                                min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="quickMonths" class="form-label">
                                Durasi (Bulan) <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="quickMonths" name="months" required>
                                <option value="">Pilih Durasi</option>
                                <option value="1">1 Bulan</option>
                                <option value="2">2 Bulan</option>
                                <option value="3" selected>3 Bulan</option>
                                <option value="6">6 Bulan</option>
                                <option value="12">12 Bulan</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Staff & Shift Assignment</h6>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addQuickStaffRow()">
                            <i class="fas fa-plus"></i>
                            Add
                        </button>
                    </div>

                    <div id="quickStaffAssignments">
                        <div class="quick-staff-row mb-3" data-index="0">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-5">
                                    <select class="form-control" name="staff_schedules[0][user_id]" required>
                                        <option value="">Pilih Staff</option>
                                        @if (isset($staff))
                                            @foreach ($staff as $s)
                                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <select class="form-control" name="staff_schedules[0][shift_id]" required>
                                        <option value="">Pilih Shift</option>
                                        @if (isset($shifts))
                                            @foreach ($shifts as $shift)
                                                <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                        onclick="removeQuickStaffRow(0)" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>
                            Untuk konfigurasi yang lebih detail, gunakan
                            <a href="{{ route('jadwal.create') }}" class="alert-link">form lengkap</a>.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-outline-primary" onclick="redirectToFullForm()">
                        <i class="fas fa-external-link-alt me-1"></i>
                        Form Lengkap
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Buat Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            let quickStaffRowIndex = 1;

            // Quick create form submission
            $('#quickCreateForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Show loading
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: '{{ route('jadwal.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#createModal').modal('hide');

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Reload the main table
                                if (typeof jadwalTable !== 'undefined') {
                                    jadwalTable.ajax.reload();
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        let message = 'Terjadi kesalahan saat membuat jadwal';

                        if (xhr.status === 422 && response.errors) {
                            // Show validation errors
                            const errors = Object.values(response.errors).flat();
                            message = errors.join('\n');
                        } else if (response.message) {
                            message = response.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message,
                            confirmButtonText: 'OK'
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Add staff row in quick form
            window.addQuickStaffRow = function() {
                const newRow = `
            <div class="quick-staff-row mb-3" data-index="${quickStaffRowIndex}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <select class="form-control" name="staff_schedules[${quickStaffRowIndex}][user_id]" required>
                            <option value="">Pilih Staff</option>
                            @if (isset($staff))
                                @foreach ($staff as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-5">
                        <select class="form-control" name="staff_schedules[${quickStaffRowIndex}][shift_id]" required>
                            <option value="">Pilih Shift</option>
                            @if (isset($shifts))
                                @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button"
                                class="btn btn-outline-danger btn-sm w-100"
                                onclick="removeQuickStaffRow(${quickStaffRowIndex})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

                $('#quickStaffAssignments').append(newRow);
                quickStaffRowIndex++;
                updateQuickRemoveButtons();
            };

            // Remove staff row in quick form
            window.removeQuickStaffRow = function(index) {
                $(`.quick-staff-row[data-index="${index}"]`).remove();
                updateQuickRemoveButtons();
            };

            function updateQuickRemoveButtons() {
                const rows = $('.quick-staff-row');
                if (rows.length <= 1) {
                    rows.find('button[onclick*="removeQuickStaffRow"]').prop('disabled', true);
                } else {
                    rows.find('button[onclick*="removeQuickStaffRow"]').prop('disabled', false);
                }
            }

            // Redirect to full form
            window.redirectToFullForm = function() {
                window.location.href = '{{ route('jadwal.create') }}';
            };

            // Reset form when modal is hidden
            $('#createModal').on('hidden.bs.modal', function() {
                $('#quickCreateForm')[0].reset();
                // Remove extra staff rows
                $('.quick-staff-row[data-index!="0"]').remove();
                updateQuickRemoveButtons();
            });
        });
    </script>
@endpush
