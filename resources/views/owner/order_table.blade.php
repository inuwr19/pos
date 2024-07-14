<table class="table table-bordered">
    <thead>
        <tr>
            <th>Order Code</th>
            <th>Customer</th>
            <th>Total Price</th>
            <th>Table Number</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>{{ $order->code_order }}</td>
            <td>{{ $order->customer }}</td>
            <td>Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
            <td>{{ $order->no_table }}</td>
            <td>{{ $order->status }}</td>
            <td>
                <a href="{{ route('ordersOwner.show', $order->id) }}" class="btn btn-primary btn-sm">View</a>
                <a href="{{ route('orders.receipt', $order->id) }}" class="btn btn-info btn-sm">View Receipt</a>
                <form action="{{ route('ordersOwner.destroy', $order->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
