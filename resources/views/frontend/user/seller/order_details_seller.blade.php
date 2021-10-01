<div class="modal-header">
    <h5 class="modal-title strong-600 heading-5">{{ translate('Order id')}}: {{ $order->code }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

@php
    $status = $order->orderDetails->where('seller_id', Auth::user()->id)->first()->delivery_status;
    $payment_status = $order->orderDetails->where('seller_id', Auth::user()->id)->first()->payment_status;
    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
@endphp

<div class="modal-body gry-bg px-3 pt-0">
    <div class="py-4">
        <div class="row gutters-5 text-center aiz-steps">
            <div class="col @if($status == 'pending') active @else done @endif">
                <div class="icon">
                    <i class="las la-file-invoice"></i>
                </div>
                <div class="title fs-12">{{ translate('Order placed')}}</div>
            </div>
            <div class="col @if($status == 'confirmed') active @elseif($status == 'on_delivery' || $status == 'delivered') done @endif">
                <div class="icon">
                    <i class="las la-newspaper"></i>
                </div>
              <div class="title fs-12">{{ translate('Confirmed')}}</div>
            </div>
            <div class="col @if($status == 'on_delivery') active @elseif($status == 'delivered') done @endif">
                <div class="icon">
                    <i class="las la-truck"></i>
                </div>
                <div class="title fs-12">{{ translate('On delivery')}}</div>
            </div>
            <div class="col @if($status == 'delivered') done @endif">
                <div class="icon">
                    <i class="las la-clipboard-check"></i>
                </div>
                <div class="title fs-12">{{ translate('Delivered')}}</div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="offset-lg-2 col-lg-4 col-sm-6">
            <div class="form-group">
                <select class="form-control aiz-selectpicker form-control-sm"  data-minimum-results-for-search="Infinity" id="update_payment_status">
                    <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>{{ translate('Unpaid')}}</option>
                    <option value="paid" @if ($payment_status == 'paid') selected @endif>{{ translate('Paid')}}</option>
                </select>
                <label>{{ translate('Payment Status')}}</label>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="form-group">
                <select class="form-control aiz-selectpicker form-control-sm"  data-minimum-results-for-search="Infinity" id="update_delivery_status">
                    <option value="pending" @if ($status == 'pending') selected @endif>{{ translate('Pending')}}</option>
                    <option value="confirmed" @if ($status == 'confirmed') selected @endif>{{ translate('Confirmed')}}</option>
                    <option value="on_delivery" @if ($status == 'on_delivery') selected @endif>{{ translate('On delivery')}}</option>
                    <option value="delivered" @if ($status == 'delivered') selected @endif>{{ translate('Delivered')}}</option>
                </select>
                <label>{{ translate('Delivery Status')}}</label>
            </div>
        </div>

        @if (Auth::check())
            @if(!empty($isCustomerBlockShop))
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-outline-danger btn-lg" onclick="unblockModal()" style="width:150px;border-radius:25px;"><i class="las la-ban"></i>{{translate('Unblock Customer')}}</button>
                </div>
            @else
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-outline-danger btn-lg" onclick="blockAlert()" style="width:150px;border-radius:25px;"><i class="las la-ban"></i>{{translate('Unblock Customer')}}</button>
                </div>
            @endif
        @endif

    </div>

    <div class="card mt-4">
        <div class="card-header">
          <b class="fs-15">{{ translate('Order Summary') }}</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    {{-- {{dd()}} --}}
                    <table class="table table-borderless">
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order Code')}}:</td>
                            <td>{{ $order->code }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Invoice')}}:</td>
                            <td>{{ $order->invoice_id }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Customer')}}:</td>
                            <td>{{ json_decode($order->shipping_address)->name }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Email')}}:</td>
                            @if ($order->user_id != null)
                            <td>
                                {{ (!empty($order->user) ? $order->user->email :'') }}
                            </td>
                            @endif
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Shipping address')}}:</td>
                            <td>{{ json_decode($order->shipping_address)->address }}, {{ json_decode($order->shipping_address)->area }}, {{ json_decode($order->shipping_address)->city }}
                                {{-- , {{ json_decode($order->shipping_address)->country }} --}}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order date')}}:</td>
                            <td>{{ date('d-m-Y H:i A', $order->date) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order status')}}:</td>
                            <td>{{ translate($status) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Total order amount')}}:</td>
                            <td>{{ single_price($order->grand_total) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Contact')}}:</td>
                            <td>{{ json_decode($order->shipping_address)->phone }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Payment method')}}:</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-9">
            <div class="card mt-4">
                <div class="card-header">
                  <b class="fs-15">{{ translate('Order Details') }}</b>
                </div>
                <div class="card-body pb-0">
                    <form id="form-add-item" action="" method="">
                        @csrf
                        <input type="hidden" name="order_id"  value="{{ $order->id }}">
                    <table class="table table-borderless table-responsive">
                        <thead>
                            <tr>
                                <th style="width: 3%">#</th>

                                <th>{{ translate('Product')}}</th>
                                <th>{{ translate('Quantity')}}</th>
                                <th>{{ translate('Price')}}</th>
                                <th>{{ translate('Available Qty')}}</th>
                                <th>{{ translate('Total Price')}}</th>
                                {{-- <th>{{ translate('Delivery Type')}}</th> --}}


                                <th class="float-right">{{ translate('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>
                                        @if ($orderDetail->product != null)
                                            <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank">{{ $orderDetail->product->product->getTranslation('name') }}</a>
                                        @else
                                            <strong>{{  translate('Product Unavailable') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $orderDetail->quantity }}
                                    </td>
                                    <td>{{ $orderDetail->product->unit_price }}</td>

                                   <td>-</td>

                                    <td>
                                        {{ $orderDetail->price }}
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('orders.delete_item', $orderDetail->id)}}" title="{{ translate('Delete') }}">
                                            <i class="las la-trash"></i>
                                        </a>
                                        {{-- <span  onclick="deleteItem({{ $orderDetail->id }});" class="btn btn-danger btn-xs" data-title="Delete"><i class="las la-trash" aria-hidden="true"></i></span> --}}
                                    </td>
                                </tr>
                            @endforeach
                            <tr id="add-item" class="not-print">
                            <td colspan="7">
                                <button type="button" class="btn btn-flat btn-success" id="add-item-button" title="Add product"><i class="fa fa-plus"></i> Add product</button>
                                &nbsp;&nbsp;&nbsp;<button style="display: none; margin-right: 50px" type="button" class="btn btn-primary" id="add-item-button-save" title="Save"><i class="fa fa-save"></i> Save</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="error_msg"></div>
                </form>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card mt-4">
                <div class="card-header">
                  <b class="fs-15">{{ translate('Order Amount') }}</b>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Subtotal')}}</th>
                                <td class="text-right">
                                    <span class="strong-600">{{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('price')) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Shipping')}}</th>
                                <td class="text-right">
                                    <span class="text-italic">{{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('shipping_cost')) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Tax')}}</th>
                                <td class="text-right">
                                    <span class="text-italic">{{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('tax')) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Coupon')}}</th>
                                <td class="text-right">
                                    <span class="text-italic">{{ single_price($order->coupon_discount) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Total')}}</th>
                                <td class="text-right">
                                    <strong>
                                        <span>{{ single_price($order->grand_total) }}
                                        </span>
                                    </strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">
                    <b class="fs-15">Delivery Type</b>
                </div>
                <div class="card-body pb-0">
                    <p class="w-50 fw-600">
                        @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                            {{  translate('Home Delivery') }}
                        @elseif ($orderDetail->shipping_type == 'pickup_point')
                            @if ($orderDetail->pickup_point != null)
                                {{ $orderDetail->pickup_point->getTranslation('name') }} ({{  translate('Pickip Point') }})
                            @endif
                        @endif
                    </p>

                    @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                        <p>{{ translate('Refund')}}</p>
                    @endif

                    @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                        <p>
                            @if ($orderDetail->product != null && $orderDetail->product->refundable != 0 && $orderDetail->refund_request == null)
                                <button type="submit" class="btn btn-primary btn-sm" onclick="send_refund_request('{{ $orderDetail->id }}')">{{  translate('Send') }}</button>
                            @elseif ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 0)
                                <b class="text-info">{{  translate('Pending') }}</b>
                            @elseif ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 1)
                                <b class="text-success">{{  translate('Paid') }}</b>
                            @endif

                        </p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
    @include('modals.delete_modal')
    {{-- Block conformation modal --}}
    <div class="modal fade bd-example-modal-sm" id="block-user-conformation-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                    <div class="modal-body">
                        <b><h5 class="font-weight-bold modal-title text-center" style="margin-bottom: 10px;
                            color: #8f939c;font-size: 30px;" id="exampleModalLabel">{{ translate('Are you sure you want to block this shop?') }}</h5></b>
                        <div class="p-1">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-outline-danger btn-lg" onclick="postBlockUserReq({{$order->user_id}})" style="width:175px;border-radius:25px;">{{translate('Yes')}}</button>
                            </div>
                            <div class="form-group text-center" style="margin-bottom: 0 !important">
                                <button type="button" class="btn btn-outline-secondary btn-lg" style="width:175px;border-radius:25px;margin-bottom:0 !important">{{translate('Dismiss')}}</button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    {{-- unblock modal --}}
    <div class="modal fade" id="unblock_request_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Send Unblock Request') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                    <form class="" action="{{ route('user.unBlockUserReq')}}" method="post">
                        @csrf
                        <input type="hidden" value={{!empty($isCustomerBlockShop) ?$isCustomerBlockShop->id :null}} name="unblockShopReqId"/>
                        <div class="modal-body gry-bg px-3 pt-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>{{ translate('Message')}}</label>
                                </div>
                                <div class="col-md-9">
                                    <textarea name="reason" rows="4" class="form-control mb-3"></textarea>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-outline-primary btn-lg" style="width:175px;border-radius:25px;">{{translate('Send')}}</button>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
@php

$htmlSelectProduct = '<tr>
    <td></td>
              <td style="width: 20%">
                <select onChange="selectProduct($(this));" name="add_id[]" class="add_id form-control aiz-selectpicker" data-live-search="true">

                <option value="0">'.translate('select product').'</option>';
                if(count($products)){

                  foreach ($products as $pId => $product){
                    //   dd($product);
                    $htmlSelectProduct .='<option  value="'.$product['id'].'" >'.$product['sku'].'</option>';
                   }
                }
  $htmlSelectProduct .='
              </select>

              <span class="add_attr"></span>
            </td>
                <td style="width: 10%"><input onChange="update_total($(this));" type="number" min="0" class="add_qty form-control" name="add_qty[]" value="0"></td>  
                <td style="width: 10%"><input onChange="update_total($(this));" type="number" min="0" class="add_price form-control" name="add_price[]" value="0" ></td>
                <td style="width: 10%"><p class="available_qty mb-0 mt-2"></p></td>
                <td style="width: 10%"><input type="text" readonly name="add_total[]" class="add_total form-control" value="0"></td>
                
                <td style="width: 10%"><button onClick="$(this).parent().parent().remove();" class="btn btn-soft-danger btn-icon btn-circle btn-sm" data-title="Delete"><i class="las la-trash" aria-hidden="true"></i></button></td>
            </tr>
          <tr>
          </tr>';
        $htmlSelectProduct = str_replace("\n", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("\t", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("\r", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("'", '"', $htmlSelectProduct);
@endphp



<script type="text/javascript">
    $('#update_delivery_status').on('change', function(){
        var order_id = {{ $order->id }};
        var status = $('#update_delivery_status').val();
        $.post('{{ route('orders.update_delivery_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status}, function(data){
            $('#order_details').modal('hide');
            AIZ.plugins.notify('success', '{{ translate('Order status has been updated') }}');
            location.reload().setTimeOut(500);
        });
    });

    $('#update_payment_status').on('change', function(){
        var order_id = {{ $order->id }};
        var status = $('#update_payment_status').val();
        $.post('{{ route('orders.update_payment_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status}, function(data){
            $('#order_details').modal('hide');
            //console.log(data);
            AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
            location.reload().setTimeOut(500);
        });
    });

    $('#add-item-button').click(function() {
        var html = '{!! $htmlSelectProduct !!}';
        $('#add-item').before(html);
        // $('.select2').select2();
        $('#add-item-button-save').show();
        $('#add-item-button-save').prop('disabled', true);
    });


    function update_total(e){
        node = e.closest('tr');
        var qty = node.find('.add_qty').eq(0).val();
        var price = node.find('.add_price').eq(0).val();
        node.find('.add_total').eq(0).val(qty*price);
    }

    function selectProduct(element){
        $('#add-item-button-save').prop('disabled', false);
        node = element.closest('tr');
        var id = parseInt(node.find('option:selected').eq(0).val());
        if(id == 0){
            node.find('.add_sku').val('');
            node.find('.add_qty').eq(0).val('');
            node.find('.add_price').eq(0).val('');
            node.find('.add_attr').html('');
            node.find('.available_qty').html('');
            node.find('.add_tax').eq(0).val('');

        }else{

            $.ajax({
                url : '{{ route('orders.product_info') }}',
                // url:'{{ url('user/getAreaByCity') }}',
                type : "GET",
                dateType:"application/json; charset=utf-8",
                data : {
                     id : id,
                     order_id : {{ $order->id }},
                },
            beforeSend: function(){
                $('#loading').show();
            },
            success: function(returnedData){
                node.find('.add_sku').val(returnedData.sku);
                node.find('.add_qty').eq(0).val(1);
                node.find('.add_price').eq(0).val(returnedData.discounted_price);
                node.find('.add_total').eq(0).val(returnedData.discounted_price);
                // node.find('.add_attr').eq(0).html(returnedData.renderAttDetails);
                node.find('.available_qty').eq(0).html(returnedData.current_stock);

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
            url:'{{ route("orders.add_item") }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'post',
            dataType:'json',

            data:$('form#form-add-item').serialize(),
            beforeSend: function(){
                $('#loading').show();
            },
            success: function(result){
                console.log('res',result);
            $('#loading').hide();
                if(parseInt(result.error) ==0){
                    AIZ.plugins.notify('success', '{{ translate('Order Item has been added successfully') }}');
                    location.reload();
                }else{
                // alertJs('error', result.msg);
                $('#error_msg').html('<p class="text-danger>'+result.msg +'</p>')
                }
            }
        });
    });

    $(".confirm-delete").click(function (e) {
        e.preventDefault();
        var url = $(this).data("href");
        $("#delete-modal").modal("show");
        $("#delete-link").attr("href", url);
    });


    function blockAlert(val){
        $('#block-user-conformation-modal').modal('show');
    }
    function postBlockUserReq(shop_id){
        $.post('{{ route('user.blockUser') }}', {_token: AIZ.data.csrf, shop_id:shop_id}, function(data){
            if(data == 1){
                AIZ.plugins.notify('success', '{{ translate('Shop Block successfully') }}');
            }
            else{
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
            $('#block-user-conformation-modal').modal('hide');
            location.reload().setTimeOut(500);
        });
    }

    function unblockModal (){
        $('#unblock_request_modal').modal('show')
    }
//End add item
</script>

