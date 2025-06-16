<x-layout>
    <div class="container">
        <h1 class="text-3xl font-bold mb-6">Create New Captain</h1>

        <form action="{{ route('captains.store') }}" method="POST" enctype="multipart/form-data" class="mt-5">
            @csrf

            <!-- Basic Information -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                <input type="text" name="name" id="name" required value="{{ old('name') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#11235A] focus:ring-[#11235A]"
                    placeholder="Enter captain's full name">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#11235A] focus:ring-[#11235A]"
                    placeholder="Enter email address">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number *</label>
                <input type="text" name="phone" id="phone" required value="{{ old('phone') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#11235A] focus:ring-[#11235A]"
                    placeholder="Enter phone number">
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="national_id" class="block text-sm font-medium text-gray-700">National ID</label>
                <input type="text" name="national_id" id="national_id" value="{{ old('national_id') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#11235A] focus:ring-[#11235A]"
                    placeholder="Enter national ID number">
                @error('national_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#11235A] focus:ring-[#11235A]">
                @error('date_of_birth')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                <input type="text" name="location" id="location" value="{{ old('location') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#11235A] focus:ring-[#11235A]"
                    placeholder="Enter location/address">
                @error('location')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Vehicle Information -->
            <h2 class="text-xl font-bold mt-8 mb-4">Vehicle Information</h2>

            <div class="mb-4">
                <label for="vehicle_model" class="block text-sm font-medium text-gray-700">Vehicle Model</label>
                <input type="text" name="vehicle_model" id="vehicle_model" value="{{ old('vehicle_model') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#11235A] focus:ring-[#11235A]"
                    placeholder="e.g., Tesla Model S">
                @error('vehicle_model')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="vehicle_color" class="block text-sm font-medium text-gray-700">Vehicle Color</label>
                <input type="text" name="vehicle_color" id="vehicle_color" value="{{ old('vehicle_color') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#11235A] focus:ring-[#11235A]"
                    placeholder="e.g., Black">
                @error('vehicle_color')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="vehicle_number" class="block text-sm font-medium text-gray-700">Vehicle Number</label>
                <input type="text" name="vehicle_number" id="vehicle_number" value="{{ old('vehicle_number') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#11235A] focus:ring-[#11235A]"
                    placeholder="e.g., 11-22222">
                @error('vehicle_number')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="vehicle_type" class="block text-sm font-medium text-gray-700">Vehicle Type</label>
                <select name="vehicle_type" id="vehicle_type"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#11235A] focus:ring-[#11235A]">
                    <option value="">Select vehicle type</option>
                    <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>Car</option>
                    <option value="motorcycle" {{ old('vehicle_type') == 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                    <option value="van" {{ old('vehicle_type') == 'van' ? 'selected' : '' }}>Van</option>
                    <option value="truck" {{ old('vehicle_type') == 'truck' ? 'selected' : '' }}>Truck</option>
                </select>
                @error('vehicle_type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Profile Image -->
            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700">Profile Image</label>
                <input type="file" name="image" id="image" accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-[#11235A] file:text-white
                    hover:file:bg-[#0d1a45]">
                @error('image')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end mt-8">
                <a href="{{ route('captains.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Cancel</a>
                <button type="submit" class="bg-[#11235A] text-[#fff] p-[15px] rounded-xl">Create Captain</button>
            </div>
        </form>

        @error('error')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</x-layout>
