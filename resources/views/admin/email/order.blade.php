<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Email</title>
</head>

<body>

    <h1>Thank you for your order!</h1>
    <h2>Your Order ID is : #{{ $mailData['order']->id }}</h2>
    <h2>Products</h2>


    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th width="100">Price</th>
                <th width="100">Qty</th>
                <th width="100">Total</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($mailData['order']->items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>${{ $item->price }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>${{ $item->total }}</td>
                </tr>
            @endforeach
            <tr>
                <th colspan="3" class="text-right">Subtotal:</th>
                <td>{{ number_format($mailData['order']->subtotal, 2) }}</td>
            </tr>
            <tr>
                <th colspan="3" class="text-right">
                    Discount:{{ !empty($mailData['order']->coupon_code) ? '(' . $mailData['order']->coupon_code . ')' : '' }}
                </th>
                <td>{{ number_format($mailData['order']->discount, 2) }}</td>
            </tr>

            <tr>
                <th colspan="3" class="text-right">Shipping:</th>
                <td>{{ number_format($mailData['order']->shipping, 2) }}</td>
            </tr>
            <tr>
                <th colspan="3" class="text-right">Grand Total:</th>
                <td>{{ number_format($mailData['order']->grand_total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
