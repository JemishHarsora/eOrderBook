@extends('frontend.layouts.app')

@section('content')

    <section class="py-5">
        <div class="container-fluid">
            <div class="d-flex align-items-start">
                @include('frontend.inc.staff_side_nav')
                <div class="aiz-user-panel">
                    <div class="row gutters-10 justify-content-center">
                        @if (Auth::user()->user_type == 'salesman')
                            <div class="col-md-4 mx-auto mb-3">
                                <a href="{{ route('orders.create') }}">
                                    <div
                                        class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition">
                                        <span
                                            class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                                            <i class="las la-plus la-3x text-white"></i>
                                        </span>
                                        <div class="fs-18 text-primary">{{ translate('Add New Order') }}</div>
                                    </div>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-12">
                        <div class="card">
                            <form id="sort_orders" action="" method="GET">
                                <div class="card-header row gutters-5">
                                    <div class="col text-center text-md-left">
                                        <h5 class="mb-md-0 h6">{{ translate('Orders') }}</h5>
                                    </div>
                                    {{-- {{dd(isset($payment_status))}} --}}
                                    <div class="col-md-3 ml-auto">
                                        <select class="form-control aiz-selectpicker"
                                            data-placeholder="{{ translate('Filter by Payment Status') }}"
                                            name="payment_status" onchange="sort_orders()">
                                            <option value="">{{ translate('Filter by Payment Status') }}</option>
                                            <option value="paid" @isset($payment_status) @if ($payment_status == 'paid') selected @endif
                                            @endisset>{{ translate('Paid') }}</option>
                                        <option value="unpaid" @isset($payment_status) @if ($payment_status == 'unpaid') selected @endif @endisset>{{ translate('Un-Paid') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-3 ml-auto">
                                    <select class="form-control aiz-selectpicker"
                                        data-placeholder="{{ translate('Filter by Payment Status') }}"
                                        name="delivery_status" onchange="sort_orders()">
                                        <option value="">{{ translate('Filter by Deliver Status') }}</option>
                                        <option value="pending" @isset($delivery_status) @if ($delivery_status == 'pending') selected @endif @endisset>{{ translate('Pending') }}</option>
                                        <option value="confirmed" @isset($delivery_status) @if ($delivery_status == 'confirmed') selected @endif @endisset>
                                            {{ translate('Confirmed') }}</option>
                                        <option value="on_delivery" @isset($delivery_status) @if ($delivery_status == 'on_delivery') selected @endif @endisset>
                                            {{ translate('On delivery') }}</option>
                                        <option value="delivered" @isset($delivery_status) @if ($delivery_status == 'delivered') selected @endif @endisset>
                                            {{ translate('Delivered') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="from-group mb-0">
                                        <input type="text" class="form-control" id="search" name="search"
                                            @isset($sort_search) value="{{ $sort_search }}" @endisset
                                            placeholder="{{ translate('Type Order code & hit Enter') }}">
                                    </div>
                                </div>
                            </div>
                        </form>

                        @if (count($orders) > 0)
                            <div class="card-body p-3">
                                <table class="table aiz-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ translate('Order Code') }}</th>
                                            <th data-breakpoints="lg">{{ translate('Num. of Products') }}</th>
                                            <th>{{ translate('Customer') }}</th>
                                            <th data-breakpoints="md">{{ translate('Amount') }}</th>
                                            <th data-breakpoints="lg">{{ translate('Delivery Status') }}</th>
                                            <th>{{ translate('Payment Status') }}</th>
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
                                                        {{ $key + 1 }}
                                                    </td>
                                                    <td>
                                                        <a href="#{{ $order->code }}"
                                                            onclick="show_order_details({{ $order->id }})">{{ $order->code }}</a>
                                                    </td>
                                                    <td>
                                                        {{ count($order->orderDetails->where('seller_id', Auth::user()->created_by)) }}
                                                    </td>
                                                    <td>
                                                        @if ($order->user_id != null)
                                                            {{ !empty($order->user) ? $order->user->name : '' }}
                                                        @else
                                                            Guest ({{ $order->guest_id }})
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ single_price($order->orderDetails->where('seller_id', Auth::user()->created_by)->sum('price')) }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $status = $order->orderDetails->first()->delivery_status;
                                                        @endphp
                                                        {{ translate(ucfirst(str_replace('_', ' ', $status))) }}
                                                    </td>
                                                    <td>
                                                        @if ($order->orderDetails->where('seller_id', Auth::user()->created_by)->first()->payment_status == 'paid')
                                                            <span
                                                                class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-inline badge-danger">{{ translate('Unpaid') }}</span>
                                                        @endif
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

</script>
@endsection
