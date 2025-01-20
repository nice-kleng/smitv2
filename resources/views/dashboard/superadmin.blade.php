<div class="container">
    <div class="row">
        {{-- <div class="col-md-3 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['totalUsers'] }}</div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Roles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['totalRoles'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>  --}}

        {{-- <div class="col-md-3 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Units</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['totalUnits'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">CPU Load</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $data['systemStats']['cpu'] > 0 ? number_format($data['systemStats']['cpu'], 2) : 'N/A' }}%
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Memory Usage</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $data['systemStats']['memory'] > 0 ? round($data['systemStats']['memory'] / 1024 / 1024, 2) : 'N/A' }} MB
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Storage Free</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $data['systemStats']['storage'] > 0 ? round($data['systemStats']['storage'] / 1024 / 1024 / 1024, 2) : 'N/A' }} GB
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['recentActivities'] as $activity)
                                <tr>
                                    <td>{{ $activity->created_at->diffForHumans() }}</td>
                                    <td>{{ $activity->user->name }}</td>
                                    <td>{{ $activity->action }}</td>
                                    <td>{{ $activity->details }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
</div>
