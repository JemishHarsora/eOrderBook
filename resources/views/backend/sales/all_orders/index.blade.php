@extends('backend.layouts.app')

@section('content')
    @php
    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
    @endphp
    <div class="card">
        <form class="" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col text-center text-md-left">
                    <h5 class="mb-md-0 h6">{{ translate('All Orders') }}</h5>
                </div>
                <div class="col-lg-2">
                    <div class="form-group mb-0">
                        <select class="form-control aiz-selectpicker" name="seller" id="seller" data-live-search="true">
                            <option value="">{{ 'Select Seller' }}</option>
                            @foreach ($sellers as $sellers)
                                <option value="{{ $sellers->id }}" @if ($seller == $sellers->id) selected @endif>{{ $sellers->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-2">
                    <div class="form-group mb-0">
                        <select class="form-control aiz-selectpicker" name="buyer" id="buyer" data-live-search="true">
                            <option value="">{{ 'Select Buyers' }}</option>
                            @foreach ($buyers as $buyers)
                                <option value="{{ $buyers->id }}" @if ($buyer == $buyers->id) selected @endif>{{ $buyers->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-2">
                    <div class="form-group mb-0">
                        <input type="text" class="aiz-date-range form-control" id="date" value="{{ $date }}"
                            name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y"
                            data-separator=" to " data-advanced-range="true" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control" id="search" name="search" @isset($sort_search)
                            value="{{ $sort_search }}" @endisset
                            placeholder="{{ translate('Type Order code & hit Enter') }}">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                    </div>
                </div>

                <div class="col-auto">
                    <div class="form-group mb-0">
                        <button type="button" class="btn btn-primary export">{{ translate('Export') }}</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Order Code') }}</th>
                        <th data-breakpoints="md">{{ translate('Num. of Products') }}</th>
                        <th data-breakpoints="md">{{ translate('Customer') }}</th>
                        <th data-breakpoints="md">{{ translate('Amount') }}</th>
                        <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                        <th data-breakpoints="md">{{ translate('Payment Status') }}</th>
                        @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                            <th>{{ translate('Refund') }}</th>
                        @endif
                        <th class="text-right" width="15%">{{ translate('options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $key => $order)

                        @if (!empty($order->orderDetails['0']))
                            <tr>
                                <td>
                                    {{ $key + 1 + ($orders->currentPage() - 1) * $orders->perPage() }}
                                </td>
                                <td>
                                    {{ $order->code }}
                                </td>
                                <td>
                                    {{ count($order->orderDetails) }}
                                </td>
                                <td>
                                    @if ($order->user != null)
                                        {{ !empty($order->user) ? $order->user->name : '' }}
                                    @else
                                        Guest ({{ $order->guest_id }})
                                    @endif
                                </td>
                                <td>
                                    {{ single_price($order->grand_total) }}
                                </td>
                                <td>
                                    @php
                                        $status = 'Delivered';
                                        foreach ($order->orderDetails as $key => $orderDetail) {
                                            if ($orderDetail->delivery_status != 'delivered') {
                                                $status = 'Pending';
                                            }
                                        }
                                    @endphp
                                    {{ translate($status) }}
                                </td>
                                <td>
                                    @if ($order->payment_status == 'paid')
                                        <span class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                                    @else
                                        <span class="badge badge-inline badge-danger">{{ translate('Unpaid') }}</span>
                                    @endif
                                </td>
                                @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                    <td>
                                        @if (count($order->refund_requests) > 0)
                                            {{ count($order->refund_requests) }} {{ translate('Refund') }}
                                        @else
                                            {{ translate('No Refund') }}
                                        @endif
                                    </td>
                                @endif
                                <td class="text-right">
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                        href="{{ route('all_orders.show', encrypt($order->id)) }}"
                                        title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                    <a class="btn btn-soft-warning btn-icon btn-circle btn-sm"
                                        href="{{ route('customer.invoice.download', $order->id) }}"
                                        title="{{ translate('Download Invoice') }}">
                                        <i class="las la-download"></i>
                                    </a>
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                        data-href="{{ route('orders.destroy', $order->id) }}"
                                        title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $orders->appends(request()->input())->links() }}
            </div>
        </div>
    </div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
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

    </script>
@endsection
