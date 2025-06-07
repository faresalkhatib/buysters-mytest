<x-layout>
    <div class="container">
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
                <td>${{ number_format($product['price'], 2) }}</td>
                <td>
                    @if($product['category_id'])
                        <a href="{{ route('category.show', $product['category_id']) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underlinee">
                            {{ $product['category'] }}
                        </a>
                    @else
                        {{ $product['category'] }}
                    @endif
                </td>
                <td>
                    <span class="badge bg-{{ $product['status'] === 'active' ? 'success' : ($product['status'] === 'pending' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($product['status']) }}
                    </span>
                </td>
                <td>{{ $product['seller'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</x-layout>
