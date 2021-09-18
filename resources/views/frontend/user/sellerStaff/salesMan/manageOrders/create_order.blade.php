@extends('frontend.layouts.app')

@section('content')

    <section class="py-5">
        <div class="container-flude">
            <div class="d-flex align-items-start">
                @if (Auth::user()->user_type == 'seller')
                    @include('frontend.inc.user_side_nav')
                @else
                    @include('frontend.inc.staff_side_nav')
                @endif
                <div class="aiz-user-panel" style="overflow: hidden">

                    <div class="aiz-titlebar mt-2 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h1 class="h3">{{ translate('Add New Order') }}</h1>
                            </div>
                        </div>
                    </div>

                    <form class="" action="{{ route('orders.addsellerorder') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="created_by" id="created_by" value={{ Auth::user()->id }}>
                        {{-- <input type="hidden" name="id" value="{{ $route->id }}"> --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Order Information') }}</h5>
                            </div>
                            <div class="card-body">

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Brand') }}</label>
                                    <div class="col-md-8">

                                        {{-- {{ dd($brands, $selected_brand) }} --}}
                                        <select class="form-control aiz-selectpicker" name="brand_id" id="brand_id"
                                            data-live-search="true" required>
                                            <option value="">Select Brand</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->brands->id }}" @isset($selected_brand) @if ($selected_brand == $brand->brands->id) selected @endif @endisset>
                                                    {{ $brand->brands->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Area') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control aiz-selectpicker" name="area_id" id="area_id"
                                            data-live-search="true" value='{{ old('area_id') }}' required>
                                            <option value="">Select Area</option>
                                            @foreach ($areas as $area)
                                                <option value="{{ $area->areas->id }}">{{ $area->areas->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label
                                        class="col-md-3 col-from-label">{{ translate('Outlet name/Shop name') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control aiz-selectpicker" name="user_id" id="user_id"
                                            data-live-search="true" required>
                                        </select>
                                    </div>
                                </div>
                                <div style="overflow-x:auto;">
                                    <table class="table table-borderless table-responsive">
                                        <thead>
                                            <tr>
                                                <th>{{ translate('Product') }}</th>
                                                {{-- <th>{{ translate('sku') }}</th> --}}
                                                <th>{{ translate('Price') }}</th>
                                                <th>{{ translate('Available Qty') }}</th>
                                                <th>{{ translate('Quantity') }}</th>
                                                {{-- <th>{{ translate('Tax') }}</th> --}}
                                                <th>{{ translate('Total Price') }}</th>
                                                <th class="float-right">{{ translate('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="add-item" class="not-print">
                                                <td colspan="7">
                                                    <button type="button" class="btn btn-flat btn-success"
                                                        id="add-item-button" title="Add product"><i class="fa fa-plus"></i>
                                                        Add product</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                        <div class="mar-all text-right">
                            <button type="submit" name="button"
                                class="btn btn-primary">{{ translate('Save Order') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection


@php

$htmlSelectProduct =
    '<tr>

              <td style="width: 20%">
                <select onChange="selectProduct($(this));" name="add_id[]" class="add_id form-control aiz-selectpicker" data-live-search="true" required>

                <option value="">' .
    translate('select product') .
    '</option>';
if (count($products)) {
    foreach ($products as $pId => $product) {
        //   dd($product);
        $htmlSelectProduct .= '<option  value="' . $product['id'] . '" >' . $product['sku'] . '</option>';
    }
}
$htmlSelectProduct .= '
              </select>

              <span class="add_attr"></span>
            </td>

              <td style="width: 10%"><input onChange="update_total($(this));" type="text" min="0" class="w-auto add_price form-control" style="width: 70px!important" name="add_price[]" value="0" ></td>
              <td style="width: 10%"><p class="available_qty mb-0 mt-2">0</p></td>
              <td style="width: 10%"><input onChange="update_total($(this));" type="number" min="0" class="w-auto add_qty form-control" name="add_qty[]" style="width: 70px!important" value="0"></td>
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

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {

            $('#brand_id').on('change', function() {
                var currentURL = window.location.href.split('?')[0];
                currentURL = currentURL + '?brand=' + this.value;
                window.location.href = currentURL;
            });

            $('#area_id').on('change', function() {
                var area_id = this.value;
                $("#user_id").html('');
                $.ajax({
                    url: "{{ url('orders/getusers') }}",
                    type: "POST",
                    data: {
                        area_id: area_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#user_id').html('<option value="">Select User</option>');
                        $.each(result.users, function(key, value) {
                            $("#user_id").append('<option value="' + value.id + '">' +
                                value.name + '</option>');
                        });
                    }
                });
            });

            $('#add-item-button').click(function() {
                var html = '{!! $htmlSelectProduct !!}';
                $('#add-item').before(html);
                // $('.select2').select2();
                $('#add-item-button-save').show();
                $('#add-item-button-save').prop('disabled', true);
            });
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
                node.find('.available_qty').eq(0).html('');
                node.find('.add_total').eq(0).val('');


                // node.find('.add_tax').eq(0).val('');

            } else {

                $.ajax({
                    url: '{{ route('orders.product_info') }}',
                    // url:'{{ url('user/getAreaByCity') }}',
                    type: "GET",
                    dateType: "application/json; charset=utf-8",
                    data: {
                        id: id,
                    },
                    beforeSend: function() {
                        $('#loading').show();
                    },
                    success: function(returnedData) {
                        node.find('.add_sku').val(returnedData.sku);
                        node.find('.add_qty').eq(0).val(1);
                        node.find('.add_price').eq(0).val(returnedData.discounted_price);
                        node.find('.add_total').eq(0).val(returnedData.discounted_price);
                        node.find('.available_qty').eq(0).html(returnedData.current_stock);
                        // node.find('.add_attr').eq(0).html(returnedData.renderAttDetails);
                        // node.find('.add_tax').eq(0).val(returnedData.taxs);

                        $('#loading').hide();
                    }
                });
            }
        }

    </script>
@endsection
