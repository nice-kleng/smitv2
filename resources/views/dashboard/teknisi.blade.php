{{-- <div class="row">
    <div class="col-md-4 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Tickets</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['activeTickets']->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Tickets</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['completedTickets'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pending Maintenance</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Location</th>
                                <th>Schedule</th>
                                <th>Priority</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['pendingMaintenance'] as $maintenance)
                            <tr>
                                <td>{{ $maintenance->asset->name }}</td>
                                <td>{{ $maintenance->asset->location }}</td>
                                <td>{{ $maintenance->schedule_date->format('d M Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $maintenance->priority === 'high' ? 'danger' :
                                        ($maintenance->priority === 'medium' ? 'warning' : 'info') }}">
                                        {{ ucfirst($maintenance->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary">Start Work</button>
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
