<x-layout>
    <div class="container">
        <h1 class="text-3xl font-bold mb-6">Products Management</h1>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <table id="mytable" border="1">
            <thead>
                <th>Product ID</th>
                <th>name</th>
                <th>price</th>
                <th>category</th>
                <th>status</th>
                <th>seller</th>
                <th>Actions</th>
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
                        <td>
                            <form action="{{ route('product.destroy', $product['id']) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete-btn bg-[#dc2626] text-white px-4 py-2 rounded-xl hover:bg-[#b91c1c] transition-colors cursor-pointer" onclick="return confirm('Are you sure you want to delete this product?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layout>
