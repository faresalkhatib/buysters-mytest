<x-layout>
    <h1>Categories</h1>
    <div class="text-right mb-5">
        <a href="/categories/create" class="bg-[#11235A] text-[#fff] p-[15px] rounded-xl">Create New Category</a>
    </div>
    <table id="mytable" border="1">
        <thead>
            <th>Category ID</th>
            <th>Category Name</th>
            <th>Image</th>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category['id'] }}</td>
                    <td>{{ $category['name'] }}</td>
                    <td>
                        <img src="{{ $category['image_url'] }}" width="25px" alt="category image">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-layout>
