<x-layout>
    <div class="container">
        <h3 class="card-title mb-4">Products in {{ $category['name'] }}</h3>

        <div class="cards">
            <div class="card">
                <h3>Total Products</h3>
                <p>{{ $statistics['total_products'] }}</p>
            </div>
            <div class="card">
                <h3>Average Price</h3>
                <p>${{ number_format($statistics['average_price'], 2) }}</p>
            </div>
            <div class="card">
                <h3>Total Value</h3>
                <p>${{ number_format($statistics['total_value'], 2) }}</p>
            </div>
        </div>

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
    </div>
</x-layout>
