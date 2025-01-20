{{-- <div class="row">
    <div class="col-md-3 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Karyawan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['totalKaryawan'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Unit</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['totalUnit'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Approvals</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['pendingApprovals'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Log Books</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Activity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['recentLogBooks'] as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d M Y') }}</td>
                                <td>{{ $log->user->name }}</td>
                                <td>{{ $log->activity }}</td>
                                <td>
                                    <span class="badge bg-{{ $log->status === 'approved' ? 'success' :
                                        ($log->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> --}}
