@extends('frontend.layouts.app')

@section('content')

    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-start">
                @include('frontend.inc.user_side_nav')
                <div class="aiz-user-panel">

                    <div class="card">
                        <form id="sort_orders" action="" method="GET">
                            <div class="card-header row gutters-5">
                                <div class="col text-center text-md-left">
                                    <h5 class="mb-md-0 h6">{{ translate('Orders') }}</h5>
                                </div>
                                <div class="col-md-3 ml-auto mb-3">
                                    <select class="form-control aiz-selectpicker" name="party" id="party"
                                        data-live-search="true" onchange="sort_orders()">
                                        <option value="">{{ 'Select Party' }}</option>
                                        @foreach ($buyers as $party)
                                        @if(isset($party->user))
                                            <option value="{{ $party->user->id }}" @isset($party_filter) @if ($party_filter == $party->user->id) selected @endif @endisset>{{ $party->user->name }}
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 ml-auto mb-3">
                                    <select class="form-control aiz-selectpicker" name="area" id="area"
                                        data-live-search="true" onchange="sort_orders()">
                                        <option value="">{{ 'Select Area' }}</option>
                                        @foreach ($areas as $area)
                                        @if(isset($area->area))
                                            <option value="{{ $area->area->id }}" @isset($area_filter) @if ($area_filter == $area->area->id) selected @endif @endisset>{{ $area->area->name }}
                                            </option>
                                        @endif
                                            @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 ml-auto mb-3">
                                    <div class="from-group mb-0">
                                        <input type="text" class="aiz-date-range form-control" id="date_filter"
                                            name="date_filter" placeholder="{{ translate('Filter by date') }}"
                                            data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true"
                                            onchange="sort_orders()" autocomplete="off" @isset($date_filter)
                                            value="{{ $date_filter }}" @endisset>
                                    </div>
                                </div>
                                <div class="col-md-3 ml-auto">
                                </div>
                                <div class="col-md-3 ml-auto mb-3">
                                    <select class="form-control aiz-selectpicker"
                                        data-placeholder="{{ translate('Filter by Payment Status') }}"
                                        name="payment_status" onchange="sort_orders()">
                                        <option value="">{{ translate('Filter by Payment Status') }}</option>
                                        <option value="paid" @isset($payment_status) @if ($payment_status == 'paid') selected @endif
                                        @endisset>{{ translate('Paid') }}</option>
                                    <option value="unpaid" @isset($payment_status) @if ($payment_status == 'unpaid') selected @endif
                                    @endisset>{{ translate('Un-Paid') }}</option>
                            </select>
                        </div>

                        <div class="col-md-3 ml-auto mb-3">
                            <select class="form-control aiz-selectpicker"
                                data-placeholder="{{ translate('Filter by Payment Status') }}"
                                name="delivery_status" onchange="sort_orders()">
                                <option value="">{{ translate('Filter by Deliver Status') }}</option>
                                <option value="pending" @isset($delivery_status) @if ($delivery_status == 'pending') selected @endif
                                @endisset>{{ translate('Pending') }}</option>
                            <option value="confirmed" @isset($delivery_status) @if ($delivery_status == 'confirmed') selected @endif @endisset>{{ translate('Confirmed') }}</option>
                            <option value="on_delivery" @isset($delivery_status) @if ($delivery_status == 'on_delivery') selected @endif @endisset>{{ translate('On delivery') }}</option>
                            <option value="delivered" @isset($delivery_status) @if ($delivery_status == 'delivered') selected @endif @endisset>{{ translate('Delivered') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 ml-auto mb-3">
                        <div class="from-group mb-0">
                            <input type="text" class="form-control" id="search" name="search"
                                onchange="sort_orders()" @isset($sort_search) value="{{ $sort_search }}"
                                @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                        </div>
                    </div>
                </div>

                <div class="card-header row gutters-5">
                    <div class="col text-center text-md-left">
                        <h5 class="mb-md-0 h6">{{ translate('Export Orders') }}</h5>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <select class="form-control aiz-selectpicker" name="buyer" id="buyer"
                                data-live-search="true">
                                <option value="">{{ 'Select Party' }}</option>
                                @foreach ($buyers as $buyers)
                                    @if ($buyers->user)
                                    <option value="{{ $buyers->user->id }}">{{ $buyers->user->name }}
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <input type="text" class="aiz-date-range form-control" id="date" name="date"
                                placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y"
                                data-separator=" to " data-advanced-range="true" autocomplete="off">
                        </div>
                    </div>
                    <input type="hidden" id="seller" value="{{ Auth::user()->id }}">
                    <div class="col-auto">
                        <div class="form-group">
                            <button type="button"
                                class="btn btn-primary export">{{ translate('Export') }}</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="card-header row gutters-5">
                <div class="col text-center text-md-left">
                    <h5 class="mb-md-0 h6">{{ translate('Update Bulk Status') }}</h5>
                </div>
                <div class="col-md-2 ml-auto mb-3 form-group">
                <label class="checkbox inline">
                    <input type="checkbox" id="select_all">Select All
                </label>
                </div>
                <div class="col-md-3 ml-auto mb-3">
                    <select class="form-control aiz-selectpicker"
                        data-placeholder="{{ translate('Update Payment Status') }}"
                        name="update_payment_status" id="update_payment_status">
                        <option value="">{{ translate('Update Payment Status') }}</option>
                        <option value="paid">{{ translate('Paid') }}</option>
                        <option value="unpaid">{{ translate('Un-Paid') }}</option>
                    </select>
                </div>

                <div class="col-md-3 ml-auto mb-3">
                    <select class="form-control aiz-selectpicker"
                            data-placeholder="{{ translate('Update Payment Status') }}"
                            name="update_delivery_status" id="update_delivery_status">
                            <option value="">{{ translate('Update Payment Status') }}</option>
                            <option value="pending">{{ translate('Pending') }}</option>
                        <option value="confirmed">{{ translate('Confirmed') }}</option>
                        <option value="on_delivery">{{ translate('On delivery') }}</option>
                        <option value="delivered">{{ translate('Delivered') }}</option>
                    </select>
                </div>

                <div class="col-auto">
                    <div class="form-group">
                        <button type="button"
                            class="btn btn-primary update">{{ translate('Update') }}</button>
                    </div>
                </div>
            </div>

            @if (count($orders) > 0)
                <div class="card-body p-3">
                    <table class="table aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th data-breakpoints="md">{{ translate('Customer') }}</th>
                                <th data-breakpoints="md">{{ translate('Num. of Items') }}</th>
                                <th data-breakpoints="md">{{ translate('Amount') }}</th>
                                <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                                <th>{{ translate('Payment Status') }}</th>
                                <th>{{ translate('Invoice') }}</th>
                                <th class="text-right">{{ translate('Options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $key => $order_id)
                                @php
                                    $order = \App\Order::find($order_id->id);
                                @endphp
                                @if ($order != null)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="order_id[]" value="{{$order->id}}">
                                            {{ $key + 1 }}
                                        </td>
                                        {{-- <td>
                                            <a href="#{{ $order->code }}"
                                                onclick="show_order_details({{ $order->id }})">{{ $order->code }}</a>
                                        </td> --}}

                                        <td>
                                            <a href="#{{ $order->code }}"
                                                onclick="show_order_details({{ $order->id }})">
                                                @if ($order->user_id != null)
                                                    {{ !empty($order->user) ? $order->user->name : '' }}
                                                @else
                                                    Guest ({{ $order->guest_id }})
                                                @endif
                                            </a>
                                        </td>
                                        <td>
                                            {{ count($order->orderDetails->where('seller_id', Auth::user()->id)) }}
                                        </td>
                                        <td>
                                            {{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('price')) }}
                                        </td>
                                        <td>
                                            @php
                                                $status = $order->orderDetails->first()->delivery_status;
                                            @endphp
                                            {{ translate(ucfirst(str_replace('_', ' ', $status))) }}
                                        </td>
                                        <td>
                                            @if ($order->orderDetails->where('seller_id', Auth::user()->id)->first()->payment_status == 'paid')
                                                <span
                                                    class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                                            @else
                                                <span
                                                    class="badge badge-inline badge-danger">{{ translate('Unpaid') }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{ $order->view_invoice == 1 ? 'Downloaded' : '' }}
                                        </td>
                                        <td class="text-right">
                                            <a href="javascript:void(0)"
                                                class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                                onclick="show_order_details({{ $order->id }})"
                                                title="{{ translate('Order Details') }}">
                                                <i class="las la-eye"></i>
                                            </a>
                                            <a href="{{ route('seller.invoice.download', $order->id) }}"
                                                class="btn btn-soft-warning btn-icon btn-circle btn-sm"
                                                title="{{ translate('Download Invoice') }}">
                                                <i class="las la-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    <div class="aiz-pagination">
                        {{ $orders->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
</section>

@endsection

@section('modal')
<div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
<div class="modal-content">
    <div id="order-details-modal-body">

    </div>
</div>
</div>
</div>
@endsection

@section('script')
<script type="text/javascript">
function sort_orders(el) {
$('#sort_orders').submit();
}

$('.export').click(function() {
var date = $('#date').val();
var buyer = $('#buyer').val();
var seller = $('#seller').val();
if (date != '' || buyer != '' || seller != '') {
    var basepth = "{{ getBaseURL() }}" + "all_orders/export?date=" + date + "&seller=" + seller +
        "&buyer=" + buyer;
    window.location.href = basepth
} else {
    AIZ.plugins.notify('danger', '{{ translate('Please select date') }}');
}
});

$('.update').click(function() {
var delivery_status = $('#update_delivery_status').val();
var payment_status = $('#update_payment_status').val();
var order_id = [];
    $.each($("input[name='order_id[]']:checked"), function(){
        order_id.push($(this).val());
    });
if ((delivery_status != '' || payment_status != '') && order_id != '') {
    var basepth = "{{ getBaseURL() }}" + "order_status/update?payment_status=" + payment_status + "&delivery_status=" + delivery_status +
        "&order_id=" + order_id;

        $.ajax({
                url : '{{ route('order_status.update') }}',
                type : "GET",
                dateType:"application/json; charset=utf-8",
                data : {
                     payment_status : payment_status,
                     delivery_status : delivery_status,
                     order_id : JSON.stringify(order_id),
                },
            beforeSend: function(){
                $('#loading').show();
            },
            success: function(result){
                AIZ.plugins.notify('success', result.message);
                window.location.href =  "{{ route('orders.index') }}"
                }
            });

    // window.location.href = basepth
} else {
    AIZ.plugins.notify('danger', '{{ translate('Please choose all required fields') }}');
}
});


    $('#select_all').change(function() {
        if($(this).is(':checked')) {
            $("input[type='checkbox']").attr('checked', 'checked');
        } else {
            $("input[type='checkbox']").removeAttr('checked');
        }
    });
        $("input[type='checkbox']").not('#select_all').change( function() {
        $('#select_all').removeAttr('checked');
    });

</script>
@endsection
