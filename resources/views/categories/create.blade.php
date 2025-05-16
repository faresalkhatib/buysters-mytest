<x-layout>
    <h1>Create New Category</h1>

    <form action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data" class="mt-5">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Category Name</label>
            <input type="text" name="name" id="name" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#11235A] focus:ring-[#11235A]">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="image" class="block text-sm font-medium text-gray-700">Category Image</label>
            <input type="file" name="image" id="image" accept="image/*" required
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

        <div class="flex items-center justify-end">
            <a href="{{ route('category') }}" class="mr-4 text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="bg-[#11235A] text-[#fff] p-[15px] rounded-xl">Create Category</button>
        </div>
    </form>
</x-layout>
