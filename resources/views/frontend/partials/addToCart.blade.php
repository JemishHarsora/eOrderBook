<div class="modal-body p-4 c-scrollbar-light">
    <div class="row">
        <div class="col-lg-5">
            <div class="row gutters-10 flex-row-reverse">
                @php
                    $photos = explode(',', $product->product->photos);
                @endphp
                <div class="col">
                    <div class="aiz-carousel product-gallery" data-nav-for='.product-gallery-thumb' data-fade='true'>
                        @foreach ($photos as $key => $photo)
                            <div class="carousel-box img-zoom rounded">
                                <img
                                    class="img-fluid lazyload"
                                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                    data-src="{{ uploaded_asset($photo) }}"
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                            </div>
                        @endforeach

                    </div>
                </div>
                <div class="col-auto w-90px h-100px">
                    <div class="aiz-carousel carousel-thumb product-gallery-thumb" data-items='3' data-nav-for='.product-gallery' data-vertical='true' data-focus-select='true'>
                        @foreach ($photos as $key => $photo)
                            <div class="carousel-box c-pointer border p-1 rounded">
                                <img
                                    class="lazyload mw-100 size-60px mx-auto"
                                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                    data-src="{{ uploaded_asset($photo) }}"
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="text-left">
                <h2 class="mb-2 fs-20 fw-600">
                    {{ $product->product->getTranslation('name') }}
                </h2>

                <div class="row align-items-center">
                    <div class="col-auto">
                        <small class="mr-2 opacity-50">{{ translate('Sold by') }}: </small><br>
                        @if ($product->product->added_by == 'seller' && \App\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
                            <a href="{{ route('shop.visit', $product->product->user->shop->slug) }}" class="text-reset">{{ $product->product->user->shop->name }}</a>
                        @else
                            {{ translate('Inhouse product') }}
                        @endif
                    </div>

                    @if ($sellersData)
                        <div class="col-auto">
                            <button class="btn btn-sm btn-soft-primary" id="moreSellerModal">{{ translate('More Seller') }}</button>
                        </div>
                    @endif

                    @if ($product->product->brand != null)
                        <div class="col-auto">
                            <img src="{{ uploaded_asset($product->product->brand->logo) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';" alt="{{ $product->product->brand->getTranslation('name') }}" height="30">
                        </div>
                    @endif
                </div>

                @if (home_price($product->id) != home_discounted_price($product->id))
                    <div class="row no-gutters mt-3">
                        <div class="col-md-6">
                            <div class="col-12">
                                <div class="opacity-50 mt-2">{{ translate('Price') }}:</div>
                            </div>
                            <div class="col-12">
                                <div class="fs-16 opacity-60">
                                    <del>
                                        {{ home_price($product->id) }}
                                        @if ($product->product->unit != null)
                                            <span>/{{ $product->product->getTranslation('unit') }}</span>
                                        @endif
                                    </del>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-12">
                                <div class="opacity-50">{{ translate('Discount Price') }}:</div>
                            </div>
                            <div class="col-12">
                                <div class="">
                                    <strong class="fs-16 fw-600 text-primary">
                                        {{ home_discounted_price($product->id) }}
                                    </strong>
                                    @if ($product->product->unit != null)
                                        <span class="opacity-70">/{{ $product->product->getTranslation('unit') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row no-gutters mt-3">
                        <div class="col-2">
                            <div class="opacity-50">{{ translate('Price') }}:</div>
                        </div>
                        <div class="col-10">
                            <div class="">
                                <strong class="h2 fw-600 text-primary">
                                    {{ home_discounted_price($product->id) }}
                                </strong>
                                <span class="opacity-70">/{{ $product->product->unit }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated && $product->product->earn_point > 0)
                    <div class="row no-gutters mt-4">
                        <div class="col-2">
                            <div class="opacity-50">{{ translate('Club Point') }}:</div>
                        </div>
                        <div class="col-10">
                            <div class="d-inline-block club-point bg-soft-base-1 border-light-base-1 border">
                                <span class="strong-700">{{ $product->product->earn_point }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <hr>

                @php
                    $qty = 0;
                    if ($product->product->variant_product) {
                        foreach ($product->product->stocks as $key => $stock) {
                            $qty += $stock->qty;
                        }
                    } else {
                        $qty = $product->product->current_stock;
                    }
                @endphp

                <form id="option-choice-form">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">

                    <!-- Quantity + Add to cart -->
                    @if ($product->product->digital != 1)
                        @if ($product->product->choice_options != null)
                            @foreach (json_decode($product->product->choice_options) as $key => $choice)

                                <div class="row no-gutters">
                                    <div class="col-2">
                                        <div class="opacity-50 mt-2 ">{{ \App\Attribute::find($choice->attribute_id)->getTranslation('name') }}:</div>
                                    </div>
                                    <div class="col-10">
                                        <div class="aiz-radio-inline">
                                            @foreach ($choice->values as $key => $value)
                                                <label class="aiz-megabox pl-0 mr-2">
                                                    <input
                                                        type="radio"
                                                        name="attribute_id_{{ $choice->attribute_id }}"
                                                        value="{{ $value }}"
                                                        @if ($key == 0) checked @endif>
                                                    <span class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center py-2 px-3 mb-2">
                                                        {{ $value }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                        @endif

                        @if (count(json_decode($product->product->colors)) > 0)
                            <div class="row no-gutters">
                                <div class="col-2">
                                    <div class="opacity-50 mt-2">{{ translate('Color') }}:</div>
                                </div>
                                <div class="col-10">
                                    <div class="aiz-radio-inline">
                                        @foreach (json_decode($product->product->colors) as $key => $color)
                                            <label class="aiz-megabox pl-0 mr-2" data-toggle="tooltip" data-title="{{ \App\Color::where('code', $color)->first()->name }}">
                                                <input
                                                    type="radio"
                                                    name="color"
                                                    value="{{ $color }}"
                                                    @if ($key == 0) checked @endif>
                                                <span class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center p-1 mb-2">
                                                    <span class="size-30px d-inline-block rounded" style="background: {{ $color }};"></span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <hr>
                        @endif

                        <div class="row no-gutters">
                            <div class="col-md-6">
                                <div class="col-2">
                                    <div class="opacity-50 mt-2">{{ translate('Quantity') }}:</div>
                                </div>
                                <div class="col-10">
                                    <div class="product-quantity d-flex align-items-center">
                                        <div class="row no-gutters align-items-center aiz-plus-minus mr-3" style="width: 130px;">
                                            <button class="btn col-auto btn-icon btn-sm btn-circle btn-light" type="button" data-type="minus" data-field={{'quantity'.$product->id}} disabled="">
                                                <i class="las la-minus"></i>
                                            </button>
                                            <input type="text" name="quantity" id={{'quantity'.$product->id}} class="col border-0 text-center flex-grow-1 fs-16 input-number" placeholder="1" value="{{ $product->min_qty }}" min="{{ $product->min_qty }}" max="{{$product->current_stock}}"> 
                                            <button class="btn  col-auto btn-icon btn-sm btn-circle btn-light" type="button" data-type="plus" data-field={{'quantity'.$product->id}}>
                                                <i class="las la-plus"></i>
                                            </button>
                                        </div>
                                        {{-- <div class="avialable-amount opacity-60">(<span id="available-quantity">{{ $qty }}</span> {{ translate('available') }})</div> --}}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row no-gutters pb-3 d-none" id="chosen_price_div">
                                    <div class="col-md-6">
                                        <div class="col-12">
                                            <div class="opacity-50">{{ translate('Total Price') }}:</div>
                                        </div>
                                        <div class="col-12">
                                            <div class="product-price">
                                                <strong id="chosen_price" class="h4 fw-600 text-primary">

                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($product->product->digital == 1)
                                    <button type="button" class="btn btn-primary buy-now fw-600 add-to-cart" {{ !empty($isblock) ? 'disabled' : '' }} onclick="addToCart()">
                                        <i class="la la-shopping-cart"></i>
                                        <span class="d-none d-md-inline-block"> {{ translate('Add to cart') }}</span>
                                    </button>
                                @elseif($qty > 0)
                                    <button type="button" class="btn btn-primary buy-now fw-600 add-to-cart" {{ !empty($isblock) ? 'disabled' : '' }} onclick="addToCart()">
                                        <i class="la la-shopping-cart"></i>
                                        <span class="d-none d-md-inline-block"> {{ translate('Add to cart') }}</span>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary fw-600" disabled>
                                        <i class="la la-cart-arrow-down"></i> {{ translate('Out of Stock') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                        <hr>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="d-none" id="more-seller">
        <div class="modal-header">
            <h6 class="modal-title fw-600">{{ translate('More sellers') }}</h6>
        </div>
        <ul class="list-group list-group-flush">
            @foreach($sellersData as $product)
            <li class="list-group-item px-0 px-lg-3">
                <div class="row">
                    <div class="col-xs-3 col-lg-2 d-flex">
                        <span class="mr-2 ml-0">
                            <img src="{{ uploaded_asset($product->product->thumbnail_img) }}"
                                class="img-fit size-60px rounded" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                alt="{{ $product->product->getTranslation('name') }}">
                        </span>
                    </div>
                    <div class="col-xs-9 col-lg-5">
                        <span class="fs-14 opacity-60">{{ $product->product->getTranslation('name') }}</span>
                        <br>
                        <span class="fw-600 fs-12">
                            @if ($product->added_by == 'seller' && \App\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
                                <a href="{{ route('shop.visit', $product->user->shop->slug) }}">{{ $product->user->shop->name }}</a>
                            @else
                                {{ translate('Inhouse product') }}
                            @endif
                        </span>
                        <br>
                        <span class="fw-600 fs-16">{{ home_discounted_price($product->id) }}
                            @if ($product->product->unit != null)
                            <span>/{{ $product->product->getTranslation('unit') }}</span>
                        @endif
                        </span>
                    </div>
                    <div class="col-xs-3 col-lg-5 px-0">
                        @if ($product->current_stock > 0)
                        <div class="row">
                        <div class="col-md-8">
                            <div class="col-2">
                                <div class="opacity-50 mt-2">{{ translate('Quantity') }}:</div>
                            </div>
                            <div class="col-10">
                                <div class="product-quantity d-flex align-items-center">
                                    <div class="row no-gutters align-items-center aiz-plus-minus mr-3" style="width: 130px;">
                                        <button class="btn col-auto btn-icon btn-sm btn-circle btn-light" type="button" data-type="minus" data-field={{'quantity'.$product->id}} disabled="">
                                            <i class="las la-minus"></i>
                                        </button>
                                        <input type="text" name="quantity" id={{'quantity'.$product->id}} class="col border-0 text-center flex-grow-1 fs-16 input-number" placeholder="1" value="{{ $product->min_qty }}" min="{{ $product->min_qty }}" max={{ $product->current_stock }}>
                                        <button class="btn  col-auto btn-icon btn-sm btn-circle btn-light" type="button" data-type="plus" data-field={{'quantity'.$product->id}}>
                                            <i class="las la-plus"></i>
                                        </button>
                                    </div>
                                    {{-- <div class="avialable-amount opacity-60">(<span id="available-quantity">{{ $qty }}</span> {{ translate('available') }})</div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mt-3">
                        <button type="button" class="btn btn-soft-primary mr-2 add-to-cart fw-600" {{ !empty($product->isblock) ? 'disabled' : '' }} onclick="addToCartFromSellerPopup('{{$product->id}}')">
                            <i class="la la-shopping-cart"></i> <span class="d-none d-md-inline-block"> </span>
                        </button>
                        @else
                            <button type="button" class="btn btn-secondary fw-600" disabled>
                                <i class="la la-cart-arrow-down"></i> 
                            </button>
                        @endif
                        </div>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>

<script type="text/javascript">
    cartQuantityInitialize();
    $('#option-choice-form input').on('change', function() {
        getVariantPrice();
    });
    $('#moreSellerModal').on('click', function(){
        var element = document.getElementById("more-seller");
        element.classList.toggle("d-none");
        // $('.more-seller').toggle('d-none');
    });

</script>
