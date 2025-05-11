<x-layout>
    <h1>welcome to products</h1>
    <table id="mytable" border="1">
        <thead>
        <th>Product ID</th>
        <th>name</th>
        <th>price</th>
        <th>category</th>
        <th>status</th>
        <th>seller</th>
        </thead>
        <tbody>
        @foreach($products as $product)
            <tr>
                <td>{{ $product['id'] }}</td>
                <td>{{ $product['name'] }}</td>
                <td>{{ $product['price'] }}</td>
                <td>{{ $product['category'] ?? 'N/A' }}</td>
                <td>{{ $product['status'] }}</td>
                <td>{{ $product['seller'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</x-layout>
