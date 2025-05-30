<x-layout>
    <h3 class="card-title mb-4">Products in {{ $category['name'] }}</h3>
        <div class="table-responsive">
            <table id="mytable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Seller</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product['name'] }}</td>
                            <td>${{ number_format($product['price'], 2) }}</td>
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
