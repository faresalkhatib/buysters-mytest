<x-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Delivery Captains</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCaptainModal">
                                <i class="fas fa-plus"></i> Add New Captain
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Total Deliveries</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($captains as $captain)
                                    <tr>
                                        <td>{{ $captain->id }}</td>
                                        <td>{{ $captain->name }}</td>
                                        <td>{{ $captain->phone }}</td>
                                        <td>{{ $captain->email }}</td>
                                        <td>
                                            <span class="badge badge-{{ $captain->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($captain->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $captain->total_deliveries }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="editCaptain({{ $captain->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteCaptain({{ $captain->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No delivery captains found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{ $captains->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Captain Modal -->
    <div class="modal fade" id="addCaptainModal" tabindex="-1" role="dialog" aria-labelledby="addCaptainModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCaptainModalLabel">Add New Delivery Captain</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('captains.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Captain</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
