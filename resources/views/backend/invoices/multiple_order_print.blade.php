<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ get_setting('site_name')->value }}</title>
</head>
<style>
    ul {
        padding: 0;
    }

    li {
        list-style: none;
    }

    table {
        border-collapse: collapse;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    td,
    th {
        padding: 3px;
    }

    th {
        font-weight: 700;
        font-size: 18px;
    }

    td,
    td,
    td {
        border: 1px solid #ddd;
    }

    p {
        margin: 0;
    }

    .aditional__info p {
        border-top: 1px dashed #ddd;
        padding: 5px 0;
    }

    .aditional__info p span {
        color: #5E23A6;
    }
</style>

<body>
@foreach ($orders as $order)
    <div class="wrapper"
        style="width: 576px;margin: auto;background-color: white;border: 1px solid #ddd; padding: 10px;">
        <div class="wrapper__header" style="text-align: center;">
            <h1 style="margin: 0;font-weight: 600;">{{ get_setting('site_name')->value }}</h1>
            <p style="font-size: 20px;">Phone: {{ get_setting('phone')->value }}</p>
            <hr style="margin: 0; margin-top: 10px;">
            <a href="{{ route('home') }}"><p style="text-decoration: underline;font-weight: 700;font-size: 16px;">www.exclusivesgarments.com</p></a>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <ul class="customer__info">
                <li>Bill To:  {{ $order->name }}</li>
                <li>Phone: {{ $order->phone ?? '' }}</li>
                @if($order->email)
                    <li>Email: {{ $order->email ?? '' }}</li>
                @endif
                <li>Address: {{ $order->address ?? 'No Address' }}</li>
            </ul>
            <ul class="customer__info">
                <li>Invoice No: {{ $order->invoice_no }}</li>
                <li>Invoice Date: {{ date('d-m-Y', strtotime($order->created_at)) }}</li>
            </ul>
        </div>
        <div class="product__info">
            <table style="width: 100%;text-align: center;">
                <tr>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Code</th>
                    <th>Qty.</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
                @foreach ($order->order_details as $key => $orderDetail)
                    @if ($orderDetail->product != null)
                        <tr>
                            <td>
                                <img src="{{ asset($orderDetail->product->product_thumbnail) }}" alt="" width="40px;">
                            </td>
                            <td>
                                {{ $orderDetail->product->name_en ?? '' }}</br>
                                @if ($orderDetail->is_varient && count(json_decode($orderDetail->variation)) > 0)
                                    @foreach (json_decode($orderDetail->variation) as $varient)
                                        {{ $varient->attribute_name }} :
                                        {{ $varient->attribute_value }}
                                    @endforeach
                                @endif
                            </td>
                            <td>{{ $orderDetail->product->product_sku ?? 'No Code' }}</td>
                            <td>{{ $orderDetail->qty }}</td>
                            <td >{{ $orderDetail->price }}</td>
                            <td>{{ $orderDetail->price * $orderDetail->qty }}</td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td colspan="5" style="text-align: end;">Sub Total (included vat)</td>
                    <td>{{ $order->sub_total }}</td>
                </tr>
                @if ($order->discount > 0)
                    <tr>
                        <td colspan="5" style="text-align: end;">Discount (-)</td>
                        <td>{{ $order->discount }}</td>
                    </tr>
                @endif
                @if ($order->shipping_charge > 0)
                    <tr>
                        <td colspan="5" style="text-align: end;">Shipping Charge (+)</td>
                        <td>{{ $order->shipping_charge }}</td>
                    </tr>
                @endif
                @if ($order->others > 0)
                    <tr>
                        <td colspan="5" style="text-align: end;">Others (+)</td>
                        <td>10</td>
                    </tr>
                @endif
                    <tr>
                        <td colspan="5" style="text-align: end;"><strong>Grand Total</strong></td>
                        <td>{{ $order->grand_total }}</td>
                    </tr>
                @if ($order->paid_amount > 0)
                    <tr>
                        <td colspan="5" style="text-align: end;">Paid</td>
                        <td>{{ $order->paid_amount }}</td>
                    </tr>
                @endif
                @if ($order->due_amount > 0)
                    <tr>
                        <td colspan="5" style="text-align: end;">Due</td>
                        <td>{{ $order->due_amount }}</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>
@endforeach
</body>
</html>
<script>
    window.onload = function() {
        window.print();
    };
</script>