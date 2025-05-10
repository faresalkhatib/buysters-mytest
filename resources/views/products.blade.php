<x-layout>
    <h1>welcome to products</h1>
    <table id="mytable" border="1">
        <thead>
        <th>index</th>
        <th>name</th>
        <th>price</th>
        <th>category</th>
        <th>status</th>
        <th>seller</th>
        </thead>
        <tbody>
        @foreach($products as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
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
