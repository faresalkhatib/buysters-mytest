<x-layout>
    <h1>Orders</h1>
    <table id="mytable" border="1">
        <thead>
            <th>Order ID</th>
            <th>Status</th>
            <th>Product</th>
            <th>Total Amount</th>
            <th>Seller Location</th>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order['id'] }}</td>
                    <td>{{ $order['status'] }}</td>
                    <td>
                        {{ $order['product_infos']['product_id'] }}
                    </td>
                    <td>
                        {{ $order['product_infos']['total_amount'] }}
                    </td>
                    <td>{{ $order['seller_location'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-layout>
