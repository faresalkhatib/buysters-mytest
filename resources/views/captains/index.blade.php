<x-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center mb-4">
                        <h3 class="card-title">Delivery Captains</h3>
                        <div class="text-right mb-5">
                            <a href="{{ route('captains.create') }}" class="bg-[#11235A] text-[#fff] p-[15px] rounded-xl hover:bg-[#0d1a45] transition-colors">Create New Captain</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="mytable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($captains as $captain)
                                    <tr>
                                        <td>{{ $captain->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if(isset($captain->image_url) && $captain->image_url)
                                                    <img src="{{ $captain->image_url }}" alt="Captain Image" class="rounded-circle mr-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $captain->name ?? 'N/A' }}</strong>
                                                    @if(isset($captain->vehicle_model))
                                                        <br><small class="text-muted">{{ $captain->vehicle_model }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $captain->phone_number ?? $captain->phone ?? 'N/A' }}</td>
                                        <td>{{ $captain->email ?? 'N/A' }}</td>
                                        <td>
                                            @if(isset($captain->status))
                                                <span class="badge badge-{{ $captain->status === 'active' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($captain->status) }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">


                                                <form action="{{ route('captains.toggleStatus', $captain->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="action-btn delete-btn bg-[#dc2626] text-white px-4 py-2 rounded-xl hover:bg-[#b91c1c] transition-colors cursor-pointer"-{{ isset($captain->status) && $captain->status === 'active' ? 'danger' : 'success' }}"
                                                            title="{{ isset($captain->status) && $captain->status === 'active' ? 'Block Captain' : 'Activate Captain' }}"
                                                            onclick="return confirm('Are you sure you want to {{ isset($captain->status) && $captain->status === 'active' ? 'block' : 'activate' }} this captain?')">
                                                        <i class="fas fa-{{ isset($captain->status) && $captain->status === 'active' ? 'ban' : 'check' }}"></i>
                                                        Change stats
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No captains found</h5>
                                                <p class="text-muted">Click "Add Captain" to create your first captain.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        @if(method_exists($captains, 'links'))
                            {{ $captains->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
