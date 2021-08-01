@extends('frontend.layouts.app')

@section('content')


    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-start">
                @include('frontend.inc.user_side_nav')

                <div class="aiz-user-panel">

                    <div class="aiz-titlebar mt-2 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h1 class="h3">{{ translate('Update your product') }}</h1>
                            </div>
                        </div>
                    </div>

                    <form class="" action="{{ route('products.update', $product->product->id) }}" method="POST"
                        enctype="multipart/form-data" id="choice_form">
                        <input name="_method" type="hidden" value="POST">
                        <input type="hidden" name="lang" value="{{ $lang }}">
                        <input type="hidden" name="id" value="{{ $product->product->id }}">
                        @csrf
                        <input type="hidden" name="added_by" value="seller">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{ translate('Product Name') }}</label>
                                    <div class="col-lg-8">

                                        <input type="text" readonly class="form-control" name="name"
                                            placeholder="{{ translate('Product Name') }}"
                                            value="{{ $product->product->getTranslation('name') }}" required>
                                    </div>
                                </div>
                                <div class="form-group row" id="category">
                                    <label class="col-lg-3 col-from-label">{{ translate('Category') }}</label>
                                    <div class="col-lg-8">
                                        <input type="text" readonly class="form-control"
                                            placeholder="{{ translate('Product Name') }}"
                                            value="{{ $category->getTranslation('name') }}" required>

                                    </div>
                                </div>
                                <div class="form-group row" id="brand">
                                    <label class="col-lg-3 col-from-label">{{ translate('Brand') }}</label>
                                    <div class="col-lg-8">
                                        <input type="text" readonly class="form-control"
                                            placeholder="{{ translate('Product Name') }}"
                                            value="{{ $brand->getTranslation('name') }}" required>

                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{ translate('Unit') }}</label>
                                    <div class="col-lg-8">
                                        <input type="text" readonly class="form-control" name="unit"
                                            placeholder="{{ translate('Unit (e.g. KG, Pc etc)') }}"
                                            value="{{ $product->product->getTranslation('unit') }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{ translate('Minimum Qty') }}</label>
                                    <div class="col-lg-8">

                                        <input type="number" lang="en" min="0" step="0.01"
                                            placeholder="{{ translate('Minimum Qty') }}" name="min_qty"
                                            class="form-control" value="{{ $product->min_qty }}" required>
                                        {{-- <input type="number" lang="en" class="form-control" name="min_qty"
                                            value="@if ($product->min_qty <= 1) {{ 1 }}@else{{ $product->min_qty }} @endif" min="1" required> --}}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{ translate('Tags') }}</label>
                                    <div class="col-lg-8">
                                        <input type="text" readonly class="form-control aiz-tag-input" name="tags[]"
                                            id="tags" value="{{ $product->product->tags }}"
                                            placeholder="{{ translate('Type to add a tag') }}" data-role="tagsinput">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('SKU') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="sku"
                                            placeholder="{{ translate('SKU Name') }}" required
                                            value="{{ $product->sku }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Manufacturer') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" readonly class="form-control" name="mfd_by"
                                            placeholder="{{ translate('Manufacturer') }}" required
                                            value="{{ $product->product->mfd_by }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Marketed By') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" readonly class="form-control" name="marketed_by"
                                            placeholder="{{ translate('Marketed By') }}" required
                                            value="{{ $product->product->marketed_by }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('HSN Code') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" readonly class="form-control" name="hsn_code"
                                            placeholder="{{ translate('HSN Code') }}" required
                                            value="{{ $product->product->hsn_code }}">
                                    </div>
                                </div>


                                {{-- @php
                                    $pos_addon = \App\Addon::where('unique_identifier', 'pos_system')->first();
                                @endphp
                                @if ($pos_addon != null && $pos_addon->activated == 1) --}}
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{ translate('Barcode') }}</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="barcode"
                                            placeholder="{{ translate('Barcode') }}"
                                            value="{{ $product->product->barcode }}" required minlength="13"
                                            maxlength="13" readonly>
                                    </div>
                                </div>
                                {{-- @endif --}}

                                @php
                                    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
                                @endphp
                                @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-from-label">{{ translate('Refundable') }}</label>
                                        <div class="col-lg-8">
                                            <label class="aiz-switch aiz-switch-success mb-0" style="margin-top:5px;">
                                                <input type="checkbox" name="refundable" @if ($product->refundable == 1) checked @endif>
                                                <span class="slider round"></span></label>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Product Images') }}</h5>
                            </div>
                            <div class="card-body">

                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label"
                                        for="signinSrEmail">{{ translate('Gallery Images') }}</label>
                                    <div class="col-md-8">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image"
                                            data-multiple="true">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                    {{ translate('Browse') }}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="photos" value="{{ $product->product->photos }}"
                                                class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label"
                                        for="signinSrEmail">{{ translate('Thumbnail Image') }}
                                        <small>(290x300)</small></label>
                                    <div class="col-md-8">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                    {{ translate('Browse') }}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="thumbnail_img"
                                                value="{{ $product->product->thumbnail_img }}" class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Product Videos') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{ translate('Video Provider') }}</label>
                                    <div class="col-lg-8">
                                        <select class="form-control aiz-selectpicker" name="video_provider"
                                            id="video_provider">
                                            <option value="youtube" <?php if ($product->
                                                product->video_provider == 'youtube') {
                                                echo 'selected';
                                                } ?> >{{ translate('Youtube') }}</option>
                                            <option value="dailymotion" <?php if ($product->
                                                product->video_provider == 'dailymotion') {
                                                echo 'selected';
                                                } ?> >{{ translate('Dailymotion') }}</option>
                                            <option value="vimeo" <?php if ($product->product->video_provider
                                                == 'vimeo') {
                                                echo 'selected';
                                                } ?> >{{ translate('Vimeo') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{ translate('Video Link') }}</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="video_link"
                                            value="{{ $product->product->video_link }}"
                                            placeholder="{{ translate('Video Link') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Product Variation') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-lg-3">
                                        <input type="text" class="form-control" value="{{ translate('Colors') }}"
                                            disabled>
                                    </div>
                                    <div class="col-lg-8">
                                        <select class="form-control aiz-selectpicker" data-live-search="true"
                                            data-selected-text-format="count" name="colors[]" id="colors" multiple>
                                            @foreach (\App\Color::orderBy('name', 'asc')->get() as $key => $color)
                                                <option value="{{ $color->code }}"
                                                    data-content="<span><span class='size-15px d-inline-block mr-2 rounded border' style='background:{{ $color->code }}'></span><span>{{ $color->name }}</span></span>"
                                                    <?php if (in_array($color->code,
                                                    json_decode($product->product->colors))) {
                                                    echo 'selected';
                                                    } ?>
                                                    ></option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-1">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input value="1" type="checkbox" name="colors_active" <?php if
                                                (count(json_decode($product->product->colors)) > 0) {
                                            echo 'checked';
                                            } ?> >
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-3">
                                        <input type="text" class="form-control" value="{{ translate('Attributes') }}"
                                            disabled>
                                    </div>
                                    <div class="col-lg-8">
                                        <select name="choice_attributes[]" data-live-search="true"
                                            data-selected-text-format="count" id="choice_attributes"
                                            class="form-control aiz-selectpicker" multiple
                                            data-placeholder="{{ translate('Choose Attributes') }}">
                                            @foreach (\App\Attribute::all() as $key => $attribute)
                                                <option value="{{ $attribute->id }}" @if ($product->product->attributes != null && in_array($attribute->id, json_decode($product->product->attributes, true))) selected @endif>
                                                    {{ $attribute->getTranslation('name') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="">
                                    <p>{{ translate('Choose the attributes of this product and then input values of each attribute') }}
                                    </p>
                                    <br>
                                </div>

                                <div class="customer_choice_options" id="customer_choice_options">
                                    @foreach (json_decode($product->product->choice_options) as $key => $choice_option)
                                        <div class="form-group row">
                                            <div class="col-lg-3">
                                                <input type="hidden" name="choice_no[]"
                                                    value="{{ $choice_option->attribute_id }}">
                                                <input type="text" class="form-control" name="choice[]"
                                                    value="{{ \App\Attribute::find($choice_option->attribute_id)->getTranslation('name') }}"
                                                    placeholder="{{ translate('Choice Title') }}" disabled>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control aiz-tag-input"
                                                    name="choice_options_{{ $choice_option->attribute_id }}[]"
                                                    placeholder="{{ translate('Enter choice values') }}"
                                                    value="{{ implode(',', $choice_option->values) }}"
                                                    data-on-change="update_sku">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Product price + stock') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{ translate('MRP') }}</label>
                                    <div class="col-lg-6">
                                        <input type="text" placeholder="{{ translate('MRP') }}" name="unit_price"
                                            class="form-control" value="{{ $product->unit_price }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{ translate('Selling price') }}</label>
                                    <div class="col-lg-6">
                                        <input type="number" lang="en" min="0" step="0.01"
                                            placeholder="{{ translate('Selling price') }}" name="purchase_price"
                                            class="form-control" value="{{ $product->purchase_price }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{ translate('Tax') }}</label>
                                    <div class="col-lg-6">
                                        <input type="number" lang="en" min="0" step="0.01"
                                            placeholder="{{ translate('tax') }}" name="tax" class="form-control"
                                            value="{{ $product->tax }}" required>
                                    </div>
                                    <div class="col-lg-3">
                                        <select class="form-control aiz-selectpicker" name="tax_type" required>
                                            <option value="amount" <?php if ($product->tax_type == 'amount')
                                                {
                                                echo 'selected';
                                                } ?> >{{ translate('Flat') }}</option>
                                            <option value="percent" <?php if ($product->tax_type ==
                                                'percent') {
                                                echo 'selected';
                                                } ?> >{{ translate('Percent') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{ translate('Discount') }}</label>
                                    <div class="col-lg-6">
                                        <input type="number" lang="en" min="0" step="0.01"
                                            placeholder="{{ translate('Discount') }}" name="discount"
                                            class="form-control" value="{{ $product->discount }}" required>
                                    </div>
                                    <div class="col-lg-3">
                                        <select class="form-control aiz-selectpicker" name="discount_type" required>
                                            <option value="amount" <?php if ($product->discount_type ==
                                                'amount') {
                                                echo 'selected';
                                                } ?> >{{ translate('Flat') }}</option>
                                            <option value="percent" <?php if ($product->discount_type ==
                                                'percent') {
                                                echo 'selected';
                                                } ?> >{{ translate('Percent') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row" id="quantity">
                                    <label class="col-lg-3 col-from-label">{{ translate('Stock') }}</label>
                                    <div class="col-lg-6">
                                        <input type="number" lang="en" value="{{ $product->current_stock }}" step="1"
                                            placeholder="{{ translate('Stock') }}" name="current_stock"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <br>
                                <div class="sku_combination" id="sku_combination">

                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Product Description') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{ translate('Description') }}</label>
                                    <div class="col-lg-9">
                                        <textarea class="aiz-text-editor"
                                            name="description">{{ $product->product->getTranslation('description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'product_wise_shipping')
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0 h6">{{ translate('Product Shipping Cost') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-lg-3">
                                            <div class="card-heading">
                                                <h5 class="mb-0 h6">{{ translate('Free Shipping') }}</h5>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-from-label">{{ translate('Status') }}</label>
                                                <div class="col-lg-8">
                                                    <label class="aiz-switch aiz-switch-success mb-0">
                                                        <input type="radio" name="shipping_type" value="free" @if ($product->product->shipping_type == 'free') checked @endif>
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-lg-3">
                                            <div class="card-heading">
                                                <h5 class="mb-0 h6">{{ translate('Flat Rate') }}</h5>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-from-label">{{ translate('Status') }}</label>
                                                <div class="col-lg-8">
                                                    <label class="aiz-switch aiz-switch-success mb-0">
                                                        <input type="radio" name="shipping_type" value="flat_rate" @if ($product->product->shipping_type == 'flat_rate') checked @endif>
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-from-label">{{ translate('Shipping cost') }}</label>
                                                <div class="col-lg-8">
                                                    <input type="number" lang="en" min="0"
                                                        value="{{ $product->product->shipping_cost }}" step="0.01"
                                                        placeholder="{{ translate('Shipping cost') }}"
                                                        name="flat_shipping_cost" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('PDF Specification') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label"
                                        for="signinSrEmail">{{ translate('PDF Specification') }}</label>
                                    <div class="col-md-8">
                                        <div class="input-group" data-toggle="aizuploader">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                    {{ translate('Browse') }}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="pdf" value="{{ $product->product->pdf }}"
                                                class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('SEO Meta Tags') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{ translate('Meta Title') }}</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="meta_title"
                                            value="{{ $product->product->meta_title }}"
                                            placeholder="{{ translate('Meta Title') }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{ translate('Description') }}</label>
                                    <div class="col-lg-8">
                                        <textarea name="meta_description" rows="8"
                                            class="form-control">{{ $product->product->meta_description }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label"
                                        for="signinSrEmail">{{ translate('Meta Images') }}</label>
                                    <div class="col-md-8">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image"
                                            data-multiple="true">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                    {{ translate('Browse') }}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="meta_img"
                                                value="{{ $product->product->meta_img }}" class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">{{ translate('Slug') }}</label>
                                    <div class="col-lg-8">
                                        <input type="text" readonly placeholder="{{ translate('Slug') }}" id="slug"
                                            name="slug" value="{{ $product->slug }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mar-all text-right">
                            <button type="submit" name="button"
                                class="btn btn-primary">{{ translate('Update Product') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script type="text/javascript">
        function add_more_customer_choice_option(i, name) {
            $('#customer_choice_options').append(
                '<div class="form-group row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' +
                i + '"><input type="text" class="form-control" name="choice[]" value="' + name +
                '" placeholder="{{ translate('Choice Title') }}" readonly></div><div class="col-md-8"><input type="text" class="form-control aiz-tag-input" name="choice_options_' +
                i +
                '[]" placeholder="{{ translate('Enter choice values') }}" data-on-change="update_sku"></div></div>');

            AIZ.plugins.tagify();
        }

        $('input[name="colors_active"]').on('change', function() {
            if (!$('input[name="colors_active"]').is(':checked')) {
                $('#colors').prop('disabled', true);
            } else {
                $('#colors').prop('disabled', false);
            }
            update_sku();
        });

        $('#colors').on('change', function() {
            update_sku();
        });

        function delete_row(em) {
            $(em).closest('.form-group').remove();
            update_sku();
        }

        function delete_variant(em) {
            $(em).closest('.variant').remove();
        }

        function update_sku() {
            $.ajax({
                type: "POST",
                url: '{{ route('products.sku_combination_edit') }}',
                data: $('#choice_form').serialize(),
                success: function(data) {
                    $('#sku_combination').html(data);
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                }
            });
        }

        AIZ.plugins.tagify();


        $(document).ready(function() {
            update_sku();

            $('.remove-files').on('click', function() {
                $(this).parents(".col-md-4").remove();
            });
        });

        $('#choice_attributes').on('change', function() {
            $.each($("#choice_attributes option:selected"), function(j, attribute) {
                flag = false;
                $('input[name="choice_no[]"]').each(function(i, choice_no) {
                    if ($(attribute).val() == $(choice_no).val()) {
                        flag = true;
                    }
                });
                if (!flag) {
                    add_more_customer_choice_option($(attribute).val(), $(attribute).text());
                }
            });

            var str = @php echo $product->attributes @endphp;

            $.each(str, function(index, value) {
                flag = false;
                $.each($("#choice_attributes option:selected"), function(j, attribute) {
                    if (value == $(attribute).val()) {
                        flag = true;
                    }
                });
                if (!flag) {
                    $('input[name="choice_no[]"][value="' + value + '"]').parent().parent().remove();
                }
            });

            update_sku();
        });

    </script>
@endsection
