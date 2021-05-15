@extends('backend.layouts.app')

@section('content')

    <div class="card">
        <div class="card-header">
            <h1 class="h2">{{ translate('Order Details') }}</h1>
        </div>
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
            </div>
            @php
                $delivery_status = $order->orderDetails->first()->delivery_status;
                $payment_status = $order->orderDetails->first()->payment_status;
            @endphp
            <div class="col-md-3 ml-auto">
                <label for=update_payment_status"">{{ translate('Payment Status') }}</label>
                <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                    id="update_payment_status">
                    <option value="paid" @if ($payment_status == 'paid') selected @endif>{{ translate('Paid') }}</option>
                    <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>{{ translate('Unpaid') }}</option>
                </select>
            </div>
            <div class="col-md-3 ml-auto">
                <label for=update_delivery_status"">{{ translate('Delivery Status') }}</label>
                <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                    id="update_delivery_status">
                    <option value="pending" @if ($delivery_status == 'pending') selected @endif>{{ translate('Pending') }}</option>
                    <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>{{ translate('Confirmed') }}</option>
                    <option value="on_delivery" @if ($delivery_status == 'on_delivery') selected @endif>{{ translate('On delivery') }}</option>
                    <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>{{ translate('Delivered') }}</option>
                </select>
            </div>
            <div class="col-lg-2">
                <button type="button" class="btn btn-danger mt-4" title="Block"><i class="las la-ban"></i> Block This
                    User</button>
            </div>
        </div>
        <div class="card-body">
            <div class="card-header row gutters-6">
                <div class="col text-center text-md-left">
                    <address>
                        <strong class="text-main">{{ json_decode($order->shipping_address)->name }}</strong><br>
                        {{ json_decode($order->shipping_address)->email }}<br>
                        {{ json_decode($order->shipping_address)->phone }}<br>
                        {{ json_decode($order->shipping_address)->address }},
                        {{ json_decode($order->shipping_address)->area }},
                        {{ json_decode($order->shipping_address)->city }}<br>
                        {{-- {{ json_decode($order->shipping_address)->country }} --}}
                    </address>
                    @if ($order->manual_payment && is_array(json_decode($order->manual_payment_data, true)))
                        <br>
                        <strong class="text-main">{{ translate('Payment Information') }}</strong><br>
                        {{ translate('Name') }}: {{ json_decode($order->manual_payment_data)->name }},
                        {{ translate('Amount') }}:
                        {{ single_price(json_decode($order->manual_payment_data)->amount) }},
                        {{ translate('TRX ID') }}: {{ json_decode($order->manual_payment_data)->trx_id }}
                        <br>
                        <a href="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}"
                            target="_blank"><img
                                src="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" alt=""
                                height="100"></a>
                    @endif
                </div>
                <div class="col-md-4 ml-auto">
                    <table>
                        <tbody>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Order #') }}</td>
                                <td class="text-right text-info text-bold"> {{ $order->code }}</td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Order Status') }}</td>
                                @php
                                    $status = $order->orderDetails->first()->delivery_status;
                                @endphp
                                <td class="text-right">
                                    @if ($status == 'delivered')
                                        <span
                                            class="badge badge-inline badge-success">{{ translate(ucfirst(str_replace('_', ' ', $status))) }}</span>
                                    @else
                                        <span
                                            class="badge badge-inline badge-info">{{ translate(ucfirst(str_replace('_', ' ', $status))) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Order Date') }} </td>
                                <td class="text-right">{{ date('d-m-Y h:i A', $order->date) }}</td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Total amount') }} </td>
                                <td class="text-right">
                                    {{ single_price($order->grand_total) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Payment method') }}</td>
                                <td class="text-right">{{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr class="new-section-sm bord-no">
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-bordered invoice-summary">
                        <thead>
                            <tr class="bg-trans-dark">
                                <th class="min-col">#</th>
                                <th width="10%">{{ translate('Photo') }}</th>
                                <th class="text-uppercase">{{ translate('Description') }}</th>
                                <th class="text-uppercase">{{ translate('Delivery Type') }}</th>
                                <th class="min-col text-center text-uppercase">{{ translate('Qty') }}</th>
                                <th class="min-col text-center text-uppercase">{{ translate('Price') }}</th>
                                <th class="min-col text-right text-uppercase">{{ translate('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderDetails as $key => $orderDetail)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        @if ($orderDetail->product != null)
                                            <a href="{{ route('product', $orderDetail->product->slug) }}"
                                                target="_blank"><img height="50"
                                                    src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}"></a>
                                        @else
                                            <strong>{{ translate('N/A') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($orderDetail->product != null)
                                            <strong><a href="{{ route('product', $orderDetail->product->slug) }}"
                                                    target="_blank"
                                                    class="text-muted">{{ $orderDetail->product->getTranslation('name') }}</a></strong>
                                            <small>{{ $orderDetail->variation }}</small>
                                        @else
                                            <strong>{{ translate('Product Unavailable') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                                            {{ translate('Home Delivery') }}
                                        @elseif ($orderDetail->shipping_type == 'pickup_point')

                                            @if ($orderDetail->pickup_point != null)
                                                {{ $orderDetail->pickup_point->getTranslation('name') }}
                                                ({{ translate('Pickup Point') }})
                                            @else
                                                {{ translate('Pickup Point') }}
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $orderDetail->quantity }}</td>
                                    <td class="text-center">
                                        {{ single_price($orderDetail->price / $orderDetail->quantity) }}</td>
                                    <td class="text-center">{{ single_price($orderDetail->price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-9">
                    <form id="form-add-item" action="" method="">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">

                        <table class="table table-borderless table-responsive">
                            <thead>
                                <tr>
                                    <th>#</th>

                                    <th>{{ translate('Product') }}</th>
                                    <th>{{ translate('sku') }}</th>
                                    <th>{{ translate('Price') }}</th>
                                    <th>{{ translate('Quantity') }}</th>
                                    <th>{{ translate('Tax') }}</th>
                                    <th>{{ translate('Total Price') }}</th>
                                    {{-- <th>{{ translate('Delivery Type')}}</th> --}}


                                    <th class="float-right">{{ translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr id="add-item" class="not-print">
                                    <td colspan="7">
                                        <button type="button" class="btn btn-flat btn-success" id="add-item-button"
                                            title="Add product"><i class="fa fa-plus"></i> Add product</button>
                                        &nbsp;&nbsp;&nbsp;<button style="display: none; margin-right: 50px" type="button"
                                            class="btn btn-primary" id="add-item-button-save" title="Save"><i
                                                class="fa fa-save"></i> Save</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>

                <div class="col-md-3 float-right">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Sub Total') }} :</strong>
                                </td>
                                <td>
                                    {{ single_price($order->orderDetails->sum('price')) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Tax') }} :</strong>
                                </td>
                                <td>
                                    {{ single_price($order->orderDetails->sum('tax')) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Shipping') }} :</strong>
                                </td>
                                <td>
                                    {{ single_price($order->orderDetails->sum('shipping_cost')) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Coupon') }} :</strong>
                                </td>
                                <td>
                                    {{ single_price($order->coupon_discount) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('TOTAL') }} :</strong>
                                </td>
                                <td class="text-muted h5">
                                    {{ single_price($order->grand_total) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-right no-print">
                        <a href="{{ route('customer.invoice.download', $order->id) }}" type="button"
                            class="btn btn-icon btn-light"><i class="las la-print"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('modals.delete_modal')
    @php

    $htmlSelectProduct =
        '<tr>
        <td></td>
                  <td style="width: 20%">
                    <select onChange="selectProduct($(this));" name="add_id[]" class="add_id form-control aiz-selectpicker" data-live-search="true">

                    <option value="0">' .
        translate('select product') .
        '</option>';
    if (count($products)) {
        foreach ($products as $pId => $product) {
            //   dd($product);
            $htmlSelectProduct .= '<option  value="' . $product['id'] . '" >' . $product['name'] . '(' . $product['sku'] . ')</option>';
        }
    }
    $htmlSelectProduct .= '
                  </select>

                  <span class="add_attr"></span>
                </td>
                  <td style="width: 20%"><input type="text" readonly class="w-auto add_sku form-control" value=""></td>
                  <td style="width: 10%"><input onChange="update_total($(this));" type="number" min="0" class="w-auto add_price form-control" name="add_price[]" value="0" ></td>
                  <td style="width: 10%"><input onChange="update_total($(this));" type="number" min="0" class="w-auto add_qty form-control" name="add_qty[]" value="0"></td>
                  <td style="width: 10%"><input onChange="update_total($(this));" type="number" min="0" readonly class="w-auto add_tax form-control" name="add_tax[]" value="0"></td>
                  <td style="width: 10%"><input type="text" readonly name="add_total[]" class="w-auto add_total form-control" value="0"></td>
                  <td style="width: 10%"><button onClick="$(this).parent().parent().remove();" class="w-auto btn btn-soft-danger btn-icon btn-circle btn-sm" data-title="Delete"><i class="las la-trash" aria-hidden="true"></i></button></td>
                </tr>
              <tr>
              </tr>';
    $htmlSelectProduct = str_replace("\n", '', $htmlSelectProduct);
    $htmlSelectProduct = str_replace("\t", '', $htmlSelectProduct);
    $htmlSelectProduct = str_replace("\r", '', $htmlSelectProduct);
    $htmlSelectProduct = str_replace("'", '"', $htmlSelectProduct);
    @endphp


@endsection



@section('script')
    <script type="text/javascript">
        $('#update_delivery_status').on('change', function() {
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();
            $.post('{{ route('orders.update_delivery_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
            });
        });

        $('#update_payment_status').on('change', function() {
            var order_id = {{ $order->id }};
            var status = $('#update_payment_status').val();
            $.post('{{ route('orders.update_payment_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
            });
        });


        $('#add-item-button').click(function() {
            var html = '{!! $htmlSelectProduct !!}';
            $('#add-item').before(html);
            // $('.select2').select2();
            $('#add-item-button-save').show();
            $('#add-item-button-save').prop('disabled', true);
        });


        function update_total(e) {
            node = e.closest('tr');
            var qty = node.find('.add_qty').eq(0).val();
            var price = node.find('.add_total').eq(0).val();
            node.find('.add_total').eq(0).val(qty * price);
        }

        function selectProduct(element) {
            $('#add-item-button-save').prop('disabled', false);
            node = element.closest('tr');
            var id = parseInt(node.find('option:selected').eq(0).val());
            if (id == 0) {
                node.find('.add_sku').val('');
                node.find('.add_qty').eq(0).val('');
                node.find('.add_price').eq(0).val('');
                node.find('.add_attr').html('');
                node.find('.add_tax').eq(0).val('');

            } else {

                $.ajax({
                    url: '{{ route('orders.product_info') }}',
                    // url:'{{ url('user/getAreaByCity') }}',
                    type: "GET",
                    dateType: "application/json; charset=utf-8",
                    data: {
                        id: id,
                        order_id: {{ $order->id }},
                    },
                    beforeSend: function() {
                        $('#loading').show();
                    },
                    success: function(returnedData) {
                        node.find('.add_sku').val(returnedData.sku);
                        node.find('.add_qty').eq(0).val(1);
                        node.find('.add_price').eq(0).val(returnedData.unit_price);
                        node.find('.add_total').eq(0).val(returnedData.discounted_price);
                        // node.find('.add_attr').eq(0).html(returnedData.renderAttDetails);
                        node.find('.add_tax').eq(0).val(returnedData.taxs);

                        $('#loading').hide();
                    }
                });



            }

        }

        $('#add-item-button-save').click(function(event) {
            $('#add-item-button').prop('disabled', true);
            $('#add-item-button-save').button('loading');
            console.log($('form#form-add-item').serialize());
            $.ajax({
                url: '{{ route('orders.add_item') }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                dataType: 'json',

                data: $('form#form-add-item').serialize(),
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(result) {
                    $('#loading').hide();
                    if (parseInt(result.error) == 0) {
                        AIZ.plugins.notify('success',
                            '{{ translate('Order Item has been added successfully') }}');
                        location.reload();
                    } else {
                        alertJs('error', result.msg);
                    }
                }
            });
        });

        $(".confirm-delete").click(function(e) {
            e.preventDefault();
            var url = $(this).data("href");
            $("#delete-modal").modal("show");
            $("#delete-link").attr("href", url);
        });

    </script>
@endsection
