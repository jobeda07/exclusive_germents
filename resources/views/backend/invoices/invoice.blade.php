<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>INVOICE</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    <style media="all">
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-size: 0.875rem;
            font-weight: normal;
            padding: 0;
            margin: 0;
        }

        .gry-color *,
        .gry-color {
            color: #000;
        }

        table {
            width: 100%;
        }

        table th {
            font-weight: normal;
        }

        table.padding th {
            padding: .25rem .7rem;
        }

        table.padding td {
            padding: .25rem .7rem;
        }

        table.sm-padding td {
            padding: .1rem .7rem;
        }

        .border-bottom td,
        .border-bottom th {
            border-bottom: 1px solid #eceff4;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div>
        <div style="background: #eceff4;padding: 1rem;">
            <table>
                <tr>
                    <td>
                        @php
                            $logo = get_setting('site_logo');
                        @endphp
                        @if ($logo != null)
                            <img src="{{ asset(get_setting('site_logo')->value ?? '') }}" alt="{{ env('APP_NAME') }}"
                                height="30" style="display:inline-block;">
                        @else
                            <img src="{{ asset('upload/no_image.jpg') }}" alt="{{ env('APP_NAME') }}" height="30"
                                style="display:inline-block;">
                        @endif
                    </td>
                    <td style="font-size: 1.5rem;" class="text-right strong">INVOICE</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="font-size: 1rem;" class="strong">{{ get_setting('site_name')->value }}</td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td class="gry-color small">Email: {{ get_setting('email')->value }} </td>
                    <td class="text-right small"><span class="gry-color small">Order ID
                            :</span> <span class="strong">{{ $order->invoice_no }}</span></td>
                </tr>
                <tr>
                    <td class="gry-color small">Phone: {{ get_setting('phone')->value }}</td>
                    <td class="text-right small"><span class="gry-color small">Order Date:</span> <span
                            class=" strong">{{ date('d-m-Y', strtotime($order->created_at)) }}</span></td>
                </tr>
                <tr>
                    <td class="gry-color small">Merchant Id: 51327</td>
                    <td class="text-right small">
                        <span class="gry-color small">
                            Payment method:
                        </span>
                        <span class=" strong">
                            @if ($order->payment_method)
                                Cash On Delivery
                            @else
                                {{ $order->payment_method }}
                            @endif
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="gry-color small">Address: Noyagaw Suchayan Academy School And College. <br />
                        Kamrangichar Dhaka-1211</td>
                </tr>
            </table>

        </div>

        <div style="padding: 1rem;padding-bottom: 0">
            <table>
                <tr>
                    <td class="strong small gry-color">Bill to:</td>
                </tr>
                <tr>
                    <td class="strong">Name: {{ $order->name }}</td>
                </tr>
                <tr>
                    <td class="gry-color small">
                        Address: {{ $order->address }}
                    </td>
                </tr>
                @if($order->email)
                    <tr>
                        <td class="gry-color small">Email: {{ $order->email }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="gry-color small">Phone: {{ $order->phone }}</td>
                </tr>
            </table>
        </div>

        <div style="padding: 1rem;">
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="gry-color" style="background: #eceff4;">
                        <th width="35%" class="text-left">Product Name</th>
                        <th width="10%" class="text-left">Qty</th>
                        <th width="15%" class="text-left">Unit Price</th>
                        <th width="15%" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    @foreach ($order->order_details as $key => $orderDetail)
                        @if ($orderDetail->product != null)
                            <tr class="">
                                <td>{{ $orderDetail->product->name_en }} ,@if ($orderDetail->is_varient && count(json_decode($orderDetail->variation)) > 0)
                                        @foreach (json_decode($orderDetail->variation) as $varient)
                                            {{ $varient->attribute_name }}, ({{ $varient->attribute_value }})
                                        @endforeach
                                    @endif
                                </td>
                                <td class="">{{ $orderDetail->qty }}</td>
                                <td class="currency">{{ $orderDetail->price }}</td>
                                <td class="text-right currency">{{ $orderDetail->price * $orderDetail->qty }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding:0 1.5rem;">
            <table class="text-right sm-padding small strong">
                <thead>
                    <tr>
                        <th width="60%"></th>
                        <th width="40%"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left">

                        </td>
                        <td>
                            <table class="text-right sm-padding small strong">
                                <tbody>
                                    <tr>
                                        <th class="gry-color text-left">Sub Total</th>
                                        <td class="currency">{{ $order->sub_total }}</td>
                                    </tr>
                                    <tr>
                                        <th class="gry-color text-left">Shipping Cost</th>
                                        <td class="currency">{{ $order->shipping_charge }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="gry-color text-left">Discount</th>
                                        <td class="currency">{{ $order->discount }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-left strong">Grand Total</th>
                                        <td class="currency">{{ $order->grand_total }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</body>

</html>
