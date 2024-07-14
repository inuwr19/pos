@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>POS System</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Product List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Product List</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($products as $category => $productsInCategory)
                            <div class="col-md-12">
                                <h4>{{ $category }}</h4>
                                <div class="row">
                                    @foreach($productsInCategory as $product)
                                    <div class="col-md-4">
                                        <div class="card">
                                            <img class="card-img-top" src="{{ asset('storage/' . $product->img) }}" alt="{{ $product->name }}">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $product->name }}</h5>
                                                <p class="card-text">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                                                <button class="btn btn-primary" onclick="addProductToOrder({{ $product->id }})">Add to Order</button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Order Summary</h3>
                    </div>
                    <div class="card-body">
                        <form id="orderForm" method="POST" action="{{ route('cashier.completeOrder') }}">
                            @csrf
                            <input type="hidden" id="orderItemsData" name="order_items">
                            <div class="form-group">
                                <label for="customer">Customer</label>
                                <input type="text" class="form-control" id="customer" name="customer" value="Customer 1" required>
                            </div>
                            <div class="form-group">
                                <label for="tableNumber">Table Number</label>
                                <input type="text" class="form-control" id="tableNumber" name="table_number" required>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="orderItems">
                                    <!-- Order items will be dynamically added here -->
                                </tbody>
                            </table>
                            <div class="form-group">
                                <label for="orderTax">Order Tax (5%)</label>
                                <input type="text" class="form-control" id="orderTax" name="order_tax" value="5%" readonly>
                            </div>
                            <div class="form-group">
                                <label for="grandTotalDisplay">Grand Total</label>
                                <input type="text" class="form-control" id="grandTotalDisplay" readonly>
                                <input type="hidden" class="form-control" id="grandTotal" name="grand_total">
                            </div>
                            <div class="form-group">
                                <label for="paymentMethod">Payment Method</label>
                                <select class="form-control" id="paymentMethod" name="payment_method" onchange="toggleCashInput(this.value)">
                                    <option value="cash">Cash</option>
                                    <option value="non_cash">Non Cash</option>
                                </select>
                            </div>
                            <div class="form-group" id="cashPayment" style="display:none;">
                                <label for="cashAmount">Cash Amount</label>
                                <input type="number" class="form-control" id="cashAmount" name="cash_amount" oninput="calculateChange()">
                                <label for="changeAmount">Change Amount</label>
                                <input type="text" class="form-control" id="changeAmount" readonly>
                            </div>
                            <button type="button" class="btn btn-secondary" onclick="resetOrder()">Reset</button>
                            <button type="submit" class="btn btn-success" id="proceedButton">Proceed</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    let products = @json($products->flatten());
    let orderItems = [];
    let taxPercentage = 5; // Default tax percentage

    function addProductToOrder(productId) {
        let product = products.find(p => p.id === productId);
        let existingProduct = orderItems.find(item => item.id === productId);

        if (existingProduct) {
            existingProduct.quantity += 1;
        } else {
            orderItems.push({
                id: product.id,
                name: product.name,
                price: product.price,
                quantity: 1
            });
        }
        renderOrderItems();
        calculateGrandTotal();
    }

    function renderOrderItems() {
        let orderItemsContainer = document.getElementById('orderItems');
        orderItemsContainer.innerHTML = '';

        orderItems.forEach(item => {
            let row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.name}</td>
                <td>Rp${item.price.toLocaleString()}</td>
                <td>
                    <input type="number" class="form-control" value="${item.quantity}" min="1" onchange="updateQuantity(${item.id}, this.value)">
                </td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="removeProductFromOrder(${item.id})">Remove</button>
                </td>
            `;
            orderItemsContainer.appendChild(row);
        });

        document.getElementById('orderItemsData').value = JSON.stringify(orderItems);
    }

    function updateQuantity(productId, quantity) {
        let product = orderItems.find(item => item.id === productId);
        product.quantity = parseInt(quantity);
        calculateGrandTotal();
    }

    function removeProductFromOrder(productId) {
        orderItems = orderItems.filter(item => item.id !== productId);
        renderOrderItems();
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let subTotal = orderItems.reduce((total, item) => total + (item.price * item.quantity), 0);
        let taxAmount = (subTotal * taxPercentage) / 100;
        let grandTotal = subTotal + taxAmount;

        document.getElementById('grandTotal').value = grandTotal; // Simpan nilai numerik
        document.getElementById('grandTotalDisplay').value = `Rp${grandTotal.toLocaleString()}`; // Tampilan untuk pengguna
    }

    function toggleCashInput(paymentMethod) {
        let cashPaymentDiv = document.getElementById('cashPayment');
        cashPaymentDiv.style.display = paymentMethod === 'cash' ? 'block' : 'none';
    }

    function calculateChange() {
        let cashAmount = parseInt(document.getElementById('cashAmount').value);
        let grandTotal = parseInt(document.getElementById('grandTotal').value.replace(/[^0-9]/g, ''));

        let changeAmount = cashAmount - grandTotal;
        document.getElementById('changeAmount').value = `Rp${changeAmount.toLocaleString()}`;
    }

    function resetOrder() {
        orderItems = [];
        renderOrderItems();
        calculateGrandTotal();
    }

    document.getElementById('orderForm').addEventListener('submit', function(event) {
        event.preventDefault();

        let formData = new FormData(this);
        formData.append('order_items', JSON.stringify(orderItems));

        let paymentMethod = document.getElementById('paymentMethod').value;
        formData.append('payment_method', paymentMethod);

        if (paymentMethod === 'non_cash') {
            // Initiate Midtrans payment
            fetch('{{ route('cashier.completeOrder') }}', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.snap_token) {
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            fetch('{{ route('midtrans.callback') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify(result),
                            }).then(() => {
                                alert('Payment successful!');
                                resetOrder();
                                printReceipt(data.order_id); // Memanggil fungsi print receipt setelah pembayaran sukses
                            });
                        },
                        onPending: function(result) {
                            alert('Waiting for payment!');
                        },
                        onError: function(result) {
                            alert('Payment failed!');
                        },
                        onClose: function() {
                            console.log('Payment popup closed');
                        }
                    });
                } else {
                    console.error('Snap token not received', data);
                }
            })
            .catch(error => console.error('Error:', error));
        } else {
            fetch('{{ route('cashier.completeOrder') }}', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                resetOrder();
                alert('Order completed successfully!');
                printReceipt(data.order_id); // Memanggil fungsi print receipt setelah pembayaran sukses
            })
            .catch(error => console.error('Error:', error));
        }
    });

    function printReceipt(orderId) {
        if (orderId) {
            window.open(`{{ route('cashier.printReceipt', '') }}/${orderId}`, '_blank');
            alert('Printing receipt...');
        } else {
            alert('No order to print receipt for.');
        }
    }
</script>
@endsection
