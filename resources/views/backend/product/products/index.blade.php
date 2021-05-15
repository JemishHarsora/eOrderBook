@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('All products') }}</h1>
            </div>
            @if ($type != 'Seller')
                <div class="col-md-6 text-md-right">
                    <a href="{{ route('products.create') }}" class="btn btn-circle btn-info">
                        <span>{{ translate('Add New Product') }}</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
    <br>

    <div class="card">
        <form class="" id="sort_products" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col text-center text-md-left">
                    <h5 class="mb-md-0 h6">{{ translate('All Product') }}</h5>
                </div>
                @if ($type == 'Seller')
                    <div class="col-md-2 ml-auto">
                        <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="user_id"
                            name="user_id" onchange="sort_products()">
                            <option value="">{{ translate('All Sellers') }}</option>
                            @foreach (App\Seller::all() as $key => $seller)
                                @if ($seller->user != null && $seller->user->shop != null)
                                    <option value="{{ $seller->user->id }}" @if ($seller->user->id == $seller_id) selected @endif>
                                        {{ $seller->user->shop->name }} ({{ $seller->user->name }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                @endif
                @if ($type == 'All')
                    <div class="col-md-2 ml-auto">
                        <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="user_id"
                            name="user_id" onchange="sort_products()">
                            <option value="">{{ translate('All Sellers') }}</option>
                            @foreach (App\User::where('user_type', '=', 'admin')
            ->orWhere('user_type', '=', 'seller')
            ->get()
        as $key => $seller)
                                <option value="{{ $seller->id }}" @if ($seller->id == $seller_id) selected @endif>{{ $seller->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-2 ml-auto">
                    <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" name="type" id="type"
                        onchange="sort_products()">
                        <option value="">{{ translate('Sort By') }}</option>
                        <option value="rating,desc" @isset($col_name, $query) @if ($col_name == 'rating' && $query == 'desc') selected @endif
                        @endisset>{{ translate('Rating (High > Low)') }}</option>
                    <option value="rating,asc" @isset($col_name, $query) @if ($col_name == 'rating' && $query == 'asc') selected @endif
                    @endisset>{{ translate('Rating (Low > High)') }}</option>
                <option value="num_of_sale,desc" @isset($col_name, $query) @if ($col_name == 'num_of_sale' && $query == 'desc') selected @endif @endisset>{{ translate('Num of Sale (High > Low)') }}</option>
                <option value="num_of_sale,asc" @isset($col_name, $query) @if ($col_name == 'num_of_sale' && $query == 'asc') selected @endif
                @endisset>{{ translate('Num of Sale (Low > High)') }}</option>
            <option value="unit_price,desc" @isset($col_name, $query) @if ($col_name == 'unit_price' && $query == 'desc') selected @endif
            @endisset>{{ translate('MRP (High > Low)') }}</option>
        <option value="unit_price,asc" @isset($col_name, $query) @if ($col_name == 'unit_price' && $query == 'asc') selected @endif
        @endisset>{{ translate('MRP (Low > High)') }}</option>
</select>
</div>
<div class="col-md-2">
<div class="form-group mb-0">
    <input type="text" class="form-control form-control-sm" id="search" name="search"
        @isset($sort_search) value="{{ $sort_search }}" @endisset
        placeholder="{{ translate('Type & Enter') }}">
</div>
</div>
</form>
<div class="col-md-2">
<div class="form-group mb-0 mt-2">
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#disableBrand">
{{ translate('Disable Brands?') }}
</button>
</div>
</div>
</div>

<div class="card-body">
<table class="table aiz-table mb-0 table-responsive">
<thead>
<tr>
<th>#</th>
<th width="20%">{{ translate('Name') }}</th>
@if ($type == 'Seller' || $type == 'All')
    <th>{{ translate('Added By') }}</th>
@endif
<th>{{ translate('SKU') }}</th>
<th>{{ translate('Num of Sale') }}</th>
<th>{{ translate('Total Stock') }}</th>
<th>{{ translate('MRP') }}</th>
<th>{{ translate('Todays Deal') }}</th>
<th>{{ translate('Rating') }}</th>
<th>{{ translate('Published') }}</th>
<th>{{ translate('Featured') }}</th>
<th class="text-right">{{ translate('Options') }}</th>
</tr>
</thead>
<tbody>
@foreach ($products as $key => $product)
<tr>
    <td>{{ $key + 1 + ($products->currentPage() - 1) * $products->perPage() }}</td>
    <td>
        <a href="{{ route('product', $product->slug) }}" target="_blank">
            <div class="form-group row">
                <div class="col-md-4">
                    <img src="{{ uploaded_asset($product->thumbnail_img) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';" alt="Image"
                        class="w-50px">
                </div>
                <div class="col-md-8">
                    <span class="text-muted">{{ $product->getTranslation('name') }}</span>
                </div>
            </div>
        </a>
    </td>
    @if ($type == 'Seller' || $type == 'All')
        <td>{{ $product->user->name }}</td>
    @endif
    <td>{{ $product->sku }}</td>
    <td>{{ $product->num_of_sale }} {{ translate('times') }}</td>
    <td>
        @php
            $qty = 0;
            if ($product->variant_product) {
                foreach ($product->stocks as $key => $stock) {
                    $qty += $stock->qty;
                }
            } else {
                $qty = $product->current_stock;
            }
            echo $qty;
        @endphp
    </td>
    <td>{{ number_format($product->unit_price, 2) }}</td>
    <td>
        <label class="aiz-switch aiz-switch-success mb-0">
            <input onchange="update_todays_deal(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->todays_deal == 1) {
            echo 'checked';
            } ?> >
            <span class="slider round"></span>
        </label>
    </td>
    <td>{{ $product->rating }}</td>
    <td>
        <label class="aiz-switch aiz-switch-success mb-0">
            <input onchange="update_published(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->published == 1) {
            echo 'checked';
            } ?> >
            <span class="slider round"></span>
        </label>
    </td>
    <td>
        <label class="aiz-switch aiz-switch-success mb-0">
            <input onchange="update_featured(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->featured == 1) {
            echo 'checked';
            } ?> >
            <span class="slider round"></span>
        </label>
    </td>
    <td class="text-right">
        @if ($type == 'Seller')
            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                href="{{ route('products.seller.edit', ['id' => $product->id, 'lang' => env('DEFAULT_LANGUAGE')]) }}"
                title="{{ translate('Edit') }}">
                <i class="las la-edit"></i>
            </a>
        @else
            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                href="{{ route('products.admin.edit', ['id' => $product->id, 'lang' => env('DEFAULT_LANGUAGE')]) }}"
                title="{{ translate('Edit') }}">
                <i class="las la-edit"></i>
            </a>
        @endif
        <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
            href="{{ route('products.duplicate', ['id' => $product->id, 'type' => $type]) }}"
            title="{{ translate('Duplicate') }}">
            <i class="las la-copy"></i>
        </a>
        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
            data-href="{{ route('products.destroy', $product->id) }}"
            title="{{ translate('Delete') }}">
            <i class="las la-trash"></i>
        </a>
    </td>
</tr>
@endforeach
</tbody>
</table>
<div class="aiz-pagination">
{{ $products->appends(request()->input())->links() }}
</div>
</div>
</div>

<div class="modal fade" id="disableBrand" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title" id="exampleModalCenterTitle">Disable Seller Brand</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
<form action="{{ route('products.disable_product') }}" method="POST">
    @csrf
    <div class="form-group mb-3">
        <label for="name">{{ translate('Seller') }}</label>
        <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" name="seller_id"
            required>
            <option value="">{{ translate('Select Sellers') }}</option>
            @foreach (App\Seller::all() as $key => $seller)
                @if ($seller->user != null && $seller->user->shop != null)
                    <option value="{{ $seller->user->id }}">{{ $seller->user->shop->name }}
                        ({{ $seller->user->name }})</option>
                @endif
            @endforeach
        </select>
    </div>

    <div class="form-group mb-3">
        <label for="name">{{ translate('Brand') }}</label>
        <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" name="brand_id"
            required>
            <option value="">{{ translate('Select Brands') }}</option>
            @foreach (App\Brand::all() as $key => $brand)
                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group mb-3">
        <label for="name">{{ translate('Select Action') }}</label>
        <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" name="action"
            required>
            <option value="1">{{ translate('Active') }}</option>
            <option value="0">{{ translate('Deactive') }}</option>
        </select>
    </div>

    <div class="form-group mb-3 text-right">
        <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
</div>
</div>
</div>
</div>
@endsection

@section('modal')
@include('modals.delete_modal')
@endsection


@section('script')
<script type="text/javascript">
$(document).ready(function() {
//$('#container').removeClass('mainnav-lg').addClass('mainnav-sm');
});

function update_todays_deal(el) {
if (el.checked) {
var status = 1;
} else {
var status = 0;
}
$.post('{{ route('products.todays_deal') }}', {
_token: '{{ csrf_token() }}',
id: el.value,
status: status
}, function(data) {
if (data == 1) {
AIZ.plugins.notify('success', '{{ translate('Todays Deal updated successfully') }}');
} else {
AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
}
});
}

function update_published(el) {
if (el.checked) {
var status = 1;
} else {
var status = 0;
}
$.post('{{ route('products.published') }}', {
_token: '{{ csrf_token() }}',
id: el.value,
status: status
}, function(data) {
if (data == 1) {
AIZ.plugins.notify('success', '{{ translate('Published products updated successfully') }}');
} else {
AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
}
});
}

function update_featured(el) {
if (el.checked) {
var status = 1;
} else {
var status = 0;
}
$.post('{{ route('products.featured') }}', {
_token: '{{ csrf_token() }}',
id: el.value,
status: status
}, function(data) {
if (data == 1) {
AIZ.plugins.notify('success', '{{ translate('Featured products updated successfully') }}');
} else {
AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
}
});
}

function sort_products(el) {
$('#sort_products').submit();
}

</script>
@endsection
