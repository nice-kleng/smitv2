@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Riwayat Closing Stok</h3>
                        <div class="card-tools">
                            <a href="{{ route('inventory.closing.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Closing Baru
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th width="15%">Periode</th>
                                        <th width="10%" class="text-center">Total Items</th>
                                        <th width="12%" class="text-center">Status</th>
                                        <th width="18%">Closed By</th>
                                        <th width="18%">Tanggal Closing</th>
                                        <th width="22%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($closings as $index => $closing)
                                        <tr>
                                            <td class="text-center">{{ $closings->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $closing->periode }}</strong>
                                                @php
                                                    $months = [
                                                        '01' => 'Januari',
                                                        '02' => 'Februari',
                                                        '03' => 'Maret',
                                                        '04' => 'April',
                                                        '05' => 'Mei',
                                                        '06' => 'Juni',
                                                        '07' => 'Juli',
                                                        '08' => 'Agustus',
                                                        '09' => 'September',
                                                        '10' => 'Oktober',
                                                        '11' => 'November',
                                                        '12' => 'Desember',
                                                    ];
                                                    [$year, $month] = explode('-', $closing->periode);
                                                @endphp
                                                <br><small class="text-muted">{{ $months[$month] }}
                                                    {{ $year }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info badge-lg">
                                                    {{ $closing->total_items }} items
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if ($closing->is_closed)
                                                    <span class="badge badge-success badge-lg">
                                                        <i class="fas fa-lock"></i> CLOSED
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning badge-lg">
                                                        <i class="fas fa-unlock"></i> DRAFT
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($closing->closedBy)
                                                    {{ $closing->closedBy->name ?? '-' }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($closing->closed_at)
                                                    {{ \Carbon\Carbon::parse($closing->closed_at)->format('d M Y H:i') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('inventory.closing.detail', $closing->periode) }}"
                                                    class="btn btn-info btn-sm" title="Detail">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>

                                                @if ($closing->is_closed)
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        onclick="reopenModal('{{ $closing->periode }}')"
                                                        title="Buka Kembali">
                                                        <i class="fas fa-unlock"></i> Reopen
                                                    </button>
                                                @else
                                                    <a href="{{ route('inventory.closing.create') }}?periode={{ $closing->periode }}"
                                                        class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <p class="text-muted my-3">Belum ada data closing</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $closings->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reopen -->
    <div class="modal fade" id="reopenModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="reopenForm" method="POST">
                    @csrf
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            <i class="fas fa-unlock"></i> Buka Kembali Closing
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Perhatian!</strong> Data closing akan dibuka kembali dan dapat diubah.
                        </div>

                        <div class="form-group">
                            <label>Periode: <span id="periodeText"></span></label>
                        </div>

                        <div class="form-group">
                            <label>Alasan Pembukaan Kembali <span class="text-danger">*</span></label>
                            <textarea name="reopen_reason" class="form-control" rows="4" placeholder="Minimal 10 karakter..." required
                                minlength="10"></textarea>
                            <small class="text-muted">Jelaskan alasan kenapa closing perlu dibuka kembali</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-unlock"></i> Buka Kembali
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function reopenModal(periode) {
                $('#periodeText').text(periode);
                $('#reopenForm').attr('action', '{{ url('inventory/closing/reopen') }}/' + periode);
                $('#reopenModal').modal('show');
            }
        </script>
    @endpush
@endsection
