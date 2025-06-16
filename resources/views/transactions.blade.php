<x-layout><div class="container">
    <h1>Transactions</h1>
    

    
  
    <div class="table-responsive">
        <table class="table table-striped" id="mytable">
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Order ID</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr>
                        <td>${{ number_format($transaction['amount'], 2) }}</td>
                        <td>{{ $transaction['date'] }}</td>
                        <td>{{ Str::limit($transaction['order_id'], 10) }}</td>
                        <td>{{ $transaction['type'] }}</td>
                        <td>
                            <span class="badge bg-{{ $transaction['status'] === 'completed' ? 'success' : 'warning' }}">
                                {{ $transaction['status'] }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('transactions.destroy', $transaction['id']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this transaction?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No transactions found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div></x-layout>

