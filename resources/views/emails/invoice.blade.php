<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME')}}</title>
    <meta http-equiv="Content-Type" content="text/html;" />
    <meta charset="UTF-8">
    <style media="all">
        @font-face {
            font-family: 'Roboto';
            src: url("{{ static_asset('fonts/Roboto-Regular.ttf') }}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }

        * {
            margin: 0;
            padding: 0;
            line-height: 1.3;
            font-family: 'Roboto';
            color: #333542;
        }

        body {
            font-size: .875rem;
        }

        .gry-color *,
        .gry-color {
            color: #878f9c;
        }

        table {
            width: 100%;
        }

        table th {
            font-weight: normal;
        }

        table.padding th {
            padding: .5rem .7rem;
        }

        table.padding td {
            padding: .7rem;
        }

        table.sm-padding td {
            padding: .2rem .7rem;
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

        .small {
            font-size: .85rem;
        }

        .currency {}

    </style>
</head>

<body>
    <div>
        @php
            $logo = get_setting('header_logo');
        @endphp
        <div style="background: #eceff4;padding: 1.5rem; margin-bottom:20px">
            <table>
                <tr>
                    <td>
                        @if ($logo != null)
                            <img loading="lazy" src="{{ uploaded_asset($logo) }}" height="40" style="display:inline-block;">
                        @else
                            <img loading="lazy" src="{{ static_asset('assets/img/logo.png') }}" height="40" style="display:inline-block;">
                        @endif
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="font-size: 1.2rem;" class="strong">{{ get_setting('site_name') }}</td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td class="gry-color small">{{ get_setting('contact_address') }}</td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td class="gry-color small">{{ translate('Email') }}: {{ get_setting('contact_email') }}</td>
                    <td class="text-right small"><span class="gry-color small">{{ translate('Order ID') }}:</span> <span class="strong">{{ $order->code }}</span></td>
                </tr>
                <tr>
                    <td class="gry-color small">{{ translate('Phone') }}: {{ get_setting('contact_phone') }}</td>
                    <td class="text-right small"><span class="gry-color small">{{ translate('Order Date') }}:</span> <span class=" strong">{{ date('d-m-Y', $order->date) }}</span></td>
                </tr>
            </table>

        </div>

        <div style="padding: 1.5rem; margin-top: 10px margin-bottom:-15px">
            <div style="padding-bottom: 0; width: 49.5%; display: inline-block;">
                <table>
                    @php
                        $shipping_address = json_decode($order->shipping_address);
                    @endphp
                    <tr><td class="strong small gry-color">{{ translate('Bill to') }}:</td></tr>
                    <tr><td class="strong">{{ $shipping_address->name }}</td></tr>
                    <tr><td class="gry-color small">{{ $shipping_address->address }}, {{ $shipping_address->area }}, {{ $shipping_address->city }}</td></tr>
                    <tr><td class="gry-color small">{{ translate('Email') }}: {{ $shipping_address->email }}</td></tr>
                    <tr><td class="gry-color small">{{ translate('Phone') }}: {{ $shipping_address->phone }}</td></tr>
                    <tr><td class="gry-color small">{{ translate('GST') }}: {{ $order->user->gst_no }}</td></tr>
                </table>
            </div>
    
            <div style="padding-bottom: 0;width: 50%;display: inline-block;">
                <table>
                
                    <tr><td class="strong small gry-color text-right">{{ translate('Bill from') }}:</td></tr>
                    <tr><td class="strong text-right">{{ $order->seller->shop->name }}</td></tr>
                    <tr><td class="gry-color small text-right">{{ $order->seller->address }}, {{ $order->seller->areas->name }}, {{ $order->seller->areas->city->name }}</td></tr>
                    <tr><td class="gry-color small text-right">{{ translate('Email') }}: {{ $order->seller->email }}</td></tr>
                    <tr><td class="gry-color small text-right">{{ translate('Phone') }}: {{ $order->seller->phone }}</td></tr>
                    <tr><td class="gry-color small text-right">{{ translate('GST') }}: {{ $order->seller->gst_no }}</td></tr>
                </table>
            </div>
        </div>

        <div>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="gry-color" style="background: #eceff4;">
                        <th class="text-left">{{ translate('Product Name') }}</th>
						<th class="text-left">{{ translate('HSN Code') }}</th>
						<th class="text-left">{{ translate('Qty') }}</th>
	                    <th class="text-left">{{ translate('Rate') }}</th>	                    
						<th class="text-left">{{ translate('CGST') }}</th>
						<th class="text-left">{{ translate('SGST') }}</th>
	                    <th class="text-left">{{ translate('Net Total') }}</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    @foreach ($order->orderDetails as $key => $orderDetail)
                        @if ($orderDetail->product != null)
                            <tr class="">
                                <td>{{ $orderDetail->product->product->name }} @if($orderDetail->product->variation != null) ({{ $orderDetail->product->variation }}) @endif</td>
								<td class="gry-color">{{ $orderDetail->product->hsn_code }}</td>
								
								<td class="gry-color">{{ $orderDetail->quantity }}</td>
								<td class="gry-color currency">{{ single_price($orderDetail->price - (($orderDetail->price/100)*18)) }}</td>
								<td class="gry-color currency">{{ single_price((($orderDetail->price)/100)*9) }}</td>
								<td class="gry-color currency">{{ single_price((($orderDetail->price)/100)*9) }}</td>
								<td class="gry-color currency">{{ single_price($orderDetail->price) }}</td>
			                    
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding:0 1.5rem;">
            <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
                <tbody>
                    <tr>
                        <th class="gry-color text-left">{{ translate('Sub Total') }}</th>
                        <td class="currency">{{ single_price($order->orderDetails->sum('price')) }}</td>
                    </tr>
                    <tr>
                        <th class="gry-color text-left">{{ translate('Shipping Cost') }}</th>
                        <td class="currency">{{ single_price($order->orderDetails->sum('shipping_cost')) }}</td>
                    </tr>
                    <tr class="border-bottom">
                        <th class="gry-color text-left">{{ translate('Total Tax') }}</th>
                        <td class="currency">{{ single_price($order->orderDetails->sum('tax')) }}</td>
                    </tr>
                    <tr class="border-bottom">
                        <th class="gry-color text-left">{{ translate('Coupon') }}</th>
                        <td class="currency">{{ single_price($order->coupon_discount) }}</td>
                    </tr>
                    <tr>
                        <th class="text-left strong">{{ translate('Grand Total') }}</th>
                        <td class="currency">{{ single_price($order->grand_total) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</body>

</html>
