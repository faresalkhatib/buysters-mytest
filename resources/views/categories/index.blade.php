<x-layout>
    <div class="container">
        <h1>Categories</h1>
    <div class="text-right mb-5">
        <a href="/categories/create" class="bg-[#11235A] text-[#fff] p-[15px] rounded-xl">Create New Category</a>
    </div>
    <table id="mytable" border="1">
        <thead>
            <th>Category ID</th>
            <th>Category Name</th>
            <th>Image</th>
            <th>Actions</th>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category['id'] }}</td>
                    <td>{{ $category['name'] }}</td>
                    <td>
                        <img src="{{ $category['image_url'] ?? '' }}" width="25px" alt="category image">
                    </td>
                    <td>
                        <a href="{{ route('category.edit', $category['id']) }}" class="bg-[#11235A] text-[#fff] px-4 py-2 rounded-xl">Edit</a>
                        <form action="{{ route('category.destroy', $category['id']) }}" method="POST" class="inline-block ml-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-[#fff] px-4 py-2 rounded-xl" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</x-layout>
