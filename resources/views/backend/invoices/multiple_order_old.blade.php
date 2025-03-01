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
            font-size: 0.575rem !important;
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

        .border-top td,
        .border-top th {
            border-top: 1px solid #eceff4;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .invoice__item {
            display: flex;
            gap: 10px;
            justify-content: center;
            padding: 10px;
        }

        /* Add this style to force a page break before each invoice */
        .invoice-page {
            page-break-before: always;
        }
    </style>
</head>

<body>
    @foreach ($orders as $order)
        <div class="invoice-page">
            <div style="padding-bottom: 200px !important;">
                <div style="background: #eceff4;padding: 0px 20px;">
                    <div class="invoice__item">
                        <img src="{{ asset('backend/images/invoice_logo.jpg') }}" style="width: 100px" height="100px"
                            alt="{{ env('APP_NAME') }}">
                        <div>
                            <div style="font-size: 1.8rem;display: flex;justify-content: center">
                                {{ get_setting('site_name')->value }}
                            </div>
                            <table>
                                <tr>
                                    <td style="text-align: center">{{ get_setting('business_address')->value }}</td>
                                </tr>
                                <tr>
                                    <td class="text-center" style="text-align: center">Phone:
                                        {{ get_setting('phone')->value }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                </div>

                <div style="padding: 0px 20px;">
                    <table>
                        <tr>
                            <td class="strong small gry-color">Bill to: @if ($order->user->role == 4)
                                    Walk-in Customer
                                @else
                                    {{ $order->user->name ?? 'Walk-in Customer' }}
                                @endif
                            </td>
                        </tr>
                        @if ($order->user->role != 4)
                            <tr>
                                <td class="gry-color small">Phone: {{ $order->phone ?? '' }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class=" small"><span class="gry-color small">Invoice No :</span> <span
                                    class="strong">{{ $order->invoice_no }}</span></td>
                        </tr>
                        <tr>
                            <td class=" small"><span class="gry-color small">Invoice Date:</span> <span
                                    class=" strong">{{ date('d-m-Y', strtotime($order->created_at)) }}</span></td>
                        </tr>
                    </table>
                </div>

                <div style="padding: 0px 20px;">
                    <table class="padding text-left small border-bottom">
                        <thead>
                            <tr class="gry-color" style="background: #eceff4;">
                                <th width="35%" class="text-left" style="font-weight: 600;">Product Name</th>
                                <th width="10%" class="text-left" style="font-weight: 600;">Qty</th>
                                <th width="15%" class="text-left" style="font-weight: 600;">Unit Price</th>
                                <th width="15%" class="text-right" style="font-weight: 600;">Total</th>
                            </tr>
                        </thead>
                        <tbody class="strong">
                            @foreach ($order->order_details as $key => $orderDetail)
                                @if ($orderDetail->product != null)
                                    <tr class="">
                                        <td>{{ $orderDetail->product->name_en ?? '' }}</td>
                                        <td class="">{{ $orderDetail->qty }}</td>
                                        <td class="currency">{{ $orderDetail->price }}</td>
                                        <td class="text-right currency">{{ $orderDetail->price * $orderDetail->qty }}
                                        </td>
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
                                    <table class="text-right small strong">
                                        <tbody>
                                            <tr>
                                                <th class="gry-color text-left">Sub Total</th>
                                                <td class="currency">{{ $order->sub_total }}</td>
                                            </tr>
                                            @if ($order->discount > 0)
                                                <tr>
                                                    <th class="gry-color text-left">Discount</th>
                                                    <td class="currency">(-){{ $order->discount }}</td>
                                                </tr>
                                            @endif
                                            @if ($order->discount > 0)
                                                <tr>
                                                    <th class="gry-color text-left">Total</th>
                                                    <td class="currency">{{ $order->sub_total - $order->discount }}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if ($order->shipping_charge > 0)
                                                <tr>
                                                    <th class="gry-color text-left">Shipping Charge</th>
                                                    <td class="currency">(+){{ $order->shipping_charge }}</td>
                                                </tr>
                                            @endif
                                            @if ($order->others > 0)
                                                <tr>
                                                    <th class="gry-color text-left">Others</th>
                                                    <td class="currency">(+){{ $order->others }}</td>
                                                </tr>
                                            @endif
                                            <tr class="border-top">
                                                <th class="text-left"
                                                    style="font-weight: 600; display: flex; font-size: 12px;">
                                                    Grand Total</th>
                                                <td class="currency" style="font-weight: 600;">
                                                    {{ $order->grand_total }}
                                                </td>
                                            </tr>
                                            @if ($order->paid_amount > 0)
                                                <tr class="">
                                                    <th class="gry-color text-left">Paid</th>
                                                    <td class="currency">{{ $order->paid_amount }}</td>
                                                </tr>
                                            @endif
                                            @if ($order->due_amount > 0)
                                                <tr class="">
                                                    <th class="gry-color text-left">Due</th>
                                                    <td class="currency">{{ $order->due_amount }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="font-size: 13px; text-align: center; margin: 10px 0px;">Developed By : <a
                            target="_blank" style="text-decoration: none;font-weight: 700; margin-left: 5px;"
                            href="https://classicit.com.bd">
                            Classic IT</a>
                    </div>
                </div>
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
