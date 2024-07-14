<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm;
            margin: 0; /* Menghilangkan margin */
            padding: 0; /* Menghilangkan padding */
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .right {
            text-align: right;
            margin-right: 10px;
        }

        .line {
            border-bottom: 1px dashed black;
            margin: 4px 0;
        }

        .bold {
            font-weight: bold;
        }

        /* Menyembunyikan bagian kosong di bawah halaman */
        @page {
            size: 58mm auto; /* Ukuran kertas dan orientasi */
            margin: 0; /* Menghilangkan margin */
        }

        @media print {
            html, body {
                width: 58mm;
                height: auto !important;
                overflow: hidden;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="center bold">BO Coffee</div>
    <div class="line"></div>
    <div class="left">Order Code:</div>
    <div class="left">{{ $order->code_order }}</div>
    <div class="left">Customer: {{ $order->customer }}</div>
    <div class="left">Table Number: {{ $order->no_table }}</div>
    <div class="line"></div>
    @php
        $subtotal = 0;
        foreach ($order->orderProducts as $orderProduct) {
            $subtotal += $orderProduct->product->price * $orderProduct->quantity;
            echo '<div class="left">' . $orderProduct->product->name . ' x ' . $orderProduct->quantity . '</div>';
            echo '<div class="right">Rp' . number_format($orderProduct->product->price * $orderProduct->quantity, 0, ',', '.') . '</div>';
        }
        $tax = $subtotal * 0.05;
        $total = $subtotal + $tax;
    @endphp
    <div class="line"></div>
    <div class="right">Tax (5%): Rp{{ number_format($tax, 0, ',', '.') }}</div>
    <div class="line"></div>
    <div class="right bold">Total: Rp{{ number_format($total, 0, ',', '.') }}</div>
    <div class="center">Thank you!</div>
</body>
</html>
