<x-layout>



    <div class="container">
  <h2>Buysters Dashboard</h2>


<div class="cards">
    <div class="card">
        <h3>Total Orders</h3>
        <p>{{ $totalOrders }}</p>
    </div>
    <div class="card">
        <h3>Products</h3>
        <p>{{ $totalProducts }}</p>
    </div>
    <div class="card">
        <h3>Customers</h3>
        <p>{{ $totalUsers }}</p>
    </div>

</div>

  <!-- Sales Chart -->
  <canvas id="salesChart" height="100"></canvas>


  <div class="table-container">
    <h3>Recent Orders</h3>
    <table id="mytable" >
      <thead>
        <tr>
          <th>#</th>
          <th>Status</th>
          <th>Customer id</th>
          <th>Product id</th>
          <th>Total price</th>
          <th>Location</th>
        </tr>
      </thead>
      <tbody>
             @foreach($orders as $order)
                <tr>
                    <td>{{ $order['id'] }}</td>
                    <td>{{ $order['status'] }}</td>
                    <td>{{ $order['buyer_id'] }}</td>
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
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
      datasets: [{
        label: 'Sales ($)',
        data: [300, 450, 320, 500, 600, 700, 400],
        borderColor: 'rgba(75, 192, 192, 1)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
</x-layout>



