@extends('backend.layouts.app')

@section('content')

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Product Stock Update') }}</h5>
        </div>
        <div class="card-body">
            <div class="alert"
                style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                <strong>{{ translate('Step 1') }}:</strong>
                <p>1. {{ translate('Download the skeleton file and fill it with proper data') }}.</p>
                <p>2. {{ translate('You can download the example file to understand how the data must be filled') }}.</p>
                <p>3.
                    {{ translate('Once you have downloaded and filled the skeleton file, upload it in the form below and submit') }}.
                </p>
                <p>4. {{ translate('After uploading products you need to edit them and set product\'s other choices') }}.
                </p>
            </div>
            <br>
            <div class="">
                <a href="{{ static_asset('download/product_stock_update_sku_demo.xlsx') }}" download><button
                        class="btn btn-info">{{ translate('Download SKU Demo CSV') }}</button></a>
                <a href="{{ static_asset('download/product_stock_update_demo.xlsx') }}" download><button
                        class="btn btn-info">{{ translate('Download Barcode Demo CSV') }}</button></a>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><strong>{{ translate('Update Product Stock') }}</strong></h5>
        </div>
        <div class="card-body">
            <form class="form-horizontal" action="{{ route('product_stock_update') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Choose CSV') }}</label>
                            <div class="col-sm-9">
                                <div class="custom-file">
                                    <label class="custom-file-label">
                                        <input type="file" name="bulk_file" class="custom-file-input" required
                                            accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel,text/comma-separated-values, text/csv, application/csv">
                                        <span class="custom-file-name">{{ translate('Choose File') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        {{-- <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Stock update with barcode?')}}</label>
                            <div class="col-sm-3">
                                <input type="checkbox" name="is_barcode">
                            </div>
                        </div> --}}

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Stock update with barcode?') }}</label>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_barcode">
                                    <span></span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-info">{{ translate('Upload CSV') }}</button>
                </div>
            </form>
        </div>
    </div>

@endsection
