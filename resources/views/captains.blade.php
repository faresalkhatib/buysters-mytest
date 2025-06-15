<x-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Delivery Captains</h3>
                        <div class="card-tools">
                          
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

    <!-- Add Captain Modal -->
    <div class="modal fade" id="addCaptainModal" tabindex="-1" role="dialog" aria-labelledby="addCaptainModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addCaptainModalLabel">
                        <i class="fas fa-user-plus mr-2"></i>Add New Captain
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('captains.store') }}" method="POST" autocomplete="on">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">
                                        <i class="fas fa-user mr-1"></i>Full Name *
                                    </label>
                                    <input type="text" class="form-control rounded-pill @error('name') is-invalid @enderror" id="name" name="name" required placeholder="Enter captain's full name" value="{{ old('name') }}">
                                    @error('name')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="font-weight-bold">
                                        <i class="fas fa-envelope mr-1"></i>Email *
                                    </label>
                                    <input type="email" class="form-control rounded-pill @error('email') is-invalid @enderror" id="email" name="email" required placeholder="Enter email address" value="{{ old('email') }}">
                                    @error('email')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="font-weight-bold">
                                        <i class="fas fa-phone mr-1"></i>Phone Number *
                                    </label>
                                    <input type="text" class="form-control rounded-pill @error('phone') is-invalid @enderror" id="phone" name="phone" required placeholder="Enter phone number" value="{{ old('phone') }}">
                                    @error('phone')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="national_id" class="font-weight-bold">
                                        <i class="fas fa-id-card mr-1"></i>National ID
                                    </label>
                                    <input type="text" class="form-control rounded-pill @error('national_id') is-invalid @enderror" id="national_id" name="national_id" placeholder="Enter national ID number" value="{{ old('national_id') }}">
                                    @error('national_id')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_of_birth" class="font-weight-bold">
                                        <i class="fas fa-calendar mr-1"></i>Date of Birth
                                    </label>
                                    <input type="date" class="form-control rounded-pill @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                    @error('date_of_birth')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location" class="font-weight-bold">
                                        <i class="fas fa-map-marker-alt mr-1"></i>Location
                                    </label>
                                    <input type="text" class="form-control rounded-pill @error('location') is-invalid @enderror" id="location" name="location" placeholder="Enter location/address" value="{{ old('location') }}">
                                    @error('location')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <!-- Vehicle Information -->
                            <div class="col-12">
                                <h6 class="text-primary font-weight-bold mb-3">
                                    <i class="fas fa-car mr-2"></i>Vehicle Information
                                </h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_model" class="font-weight-bold">Vehicle Model</label>
                                    <input type="text" class="form-control rounded-pill @error('vehicle_model') is-invalid @enderror" id="vehicle_model" name="vehicle_model" placeholder="e.g., Tesla Model S" value="{{ old('vehicle_model') }}">
                                    @error('vehicle_model')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_color" class="font-weight-bold">Vehicle Color</label>
                                    <input type="text" class="form-control rounded-pill @error('vehicle_color') is-invalid @enderror" id="vehicle_color" name="vehicle_color" placeholder="e.g., Black" value="{{ old('vehicle_color') }}">
                                    @error('vehicle_color')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_number" class="font-weight-bold">Vehicle Number</label>
                                    <input type="text" class="form-control rounded-pill @error('vehicle_number') is-invalid @enderror" id="vehicle_number" name="vehicle_number" placeholder="e.g., 11-22222" value="{{ old('vehicle_number') }}">
                                    @error('vehicle_number')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_type" class="font-weight-bold">Vehicle Type</label>
                                    <select class="form-control rounded-pill @error('vehicle_type') is-invalid @enderror" id="vehicle_type" name="vehicle_type">
                                        <option value="">Select vehicle type</option>
                                        <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>Car</option>
                                        <option value="motorcycle" {{ old('vehicle_type') == 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                                        <option value="van" {{ old('vehicle_type') == 'van' ? 'selected' : '' }}>Van</option>
                                        <option value="truck" {{ old('vehicle_type') == 'truck' ? 'selected' : '' }}>Truck</option>
                                    </select>
                                    @error('vehicle_type')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <!-- Payment Information -->
                            <div class="col-12">
                                <h6 class="text-primary font-weight-bold mb-3">
                                    <i class="fas fa-credit-card mr-2"></i>Payment Information (Optional)
                                </h6>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="stripe_payment_method_id" class="font-weight-bold">Stripe Payment Method ID</label>
                                    <input type="text" class="form-control rounded-pill @error('stripe_payment_method_id') is-invalid @enderror" id="stripe_payment_method_id" name="stripe_payment_method_id" placeholder="Leave empty if not available" value="{{ old('stripe_payment_method_id') }}">
                                    <small class="form-text text-muted">This will be auto-generated when captain sets up payment method</small>
                                    @error('stripe_payment_method_id')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Profile Image -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="image_url" class="font-weight-bold">
                                        <i class="fas fa-image mr-1"></i>Profile Image URL
                                    </label>
                                    <input type="url" class="form-control rounded-pill @error('image_url') is-invalid @enderror" id="image_url" name="image_url" placeholder="Enter profile image URL (optional)" value="{{ old('image_url') }}">
                                    <small class="form-text text-muted">You can upload image to Firebase Storage and paste the URL here</small>
                                    @error('image_url')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-success rounded-pill">
                            <i class="fas fa-save mr-1"></i>Save Captain
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editCaptain(id) {
            // TODO: Implement edit logic here, e.g., open an edit modal and load captain data via AJAX.
            // Example: $('#editCaptainModal').modal('show'); // and load data into the modal
        }

        function deleteCaptain(id) {
            if (confirm('Are you sure you want to delete this captain?')) {
                // TODO: Implement delete logic here, e.g., send AJAX request or submit a hidden form.
                // Example: document.getElementById('delete-captain-form-' + id).submit();
            }
        }
    </script>
</x-layout>