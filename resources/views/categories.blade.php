<x-layout>
    <h1>Categories</h1>
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
