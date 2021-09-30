@extends('frontend.layouts.app')

@section('content')

    <section class="py-5">
        <div class="container-fluid">
            <div class="d-flex align-items-start">
                @include('frontend.inc.user_side_nav')

                <div class="aiz-user-panel">

                    <div class="aiz-titlebar mt-2 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h1 class="h3">{{ translate('Product Detail Update') }}</h1>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <table class="table aiz-table mb-0"
                                style="font-size:14px; background-color: #cce5ff; border-color: #b8daff">
                                <tr>
                                    <td>{{ translate('1. Download the skeleton file and fill it with data.') }}:</td>
                                </tr>
                                <tr>
                                    <td>{{ translate('2. You can download the example file to understand how the data must be filled.') }}:
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ translate('3. Once you have downloaded and filled the skeleton file, upload it in the form below and submit.') }}:
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ translate('4. After uploading products you need to edit them and set products images and choices.') }}
                                    </td>
                                </tr>
                            </table>
                            <a href="{{ static_asset('download/product_details_update_demo.xlsx') }}" download><button
                                    class="btn btn-primary mt-2">{{ translate('Download Demo CSV') }}</button></a>
                            <a href="{{ static_asset('download/product_details_update_barcode_demo.xlsx') }}" download><button class="btn btn-primary mt-2">{{ translate('Download Barcode Demo CSV')}}</button></a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6"><strong>{{ translate('Get Available Products') }}</strong></h5>
                        </div>
                        <div class="card-body">
                            <form class="form-horizontal" action="{{ route('available_product_detail') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-from-label">{{ translate('Choose brand') }}</label>
                                            <div class="col-sm-9">
                                                <select class="form-control aiz-selectpicker" name="brand_id" id="brand_id" data-live-search="true">
                                                    <option value="">{{ 'Select Brand' }}</option>
                                                    @foreach ($brands as $brand)
                                                        <option value="{{ $brand->id }}">{{ $brand->getTranslation('name') }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-6">
                                       
                                    </div>
                                </div>

                                <div class="form-group mb-0 text-right">
                                    <button type="submit" class="btn btn-primary">{{ translate('Go') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6"><strong>{{ translate('Update Bulk Products') }}</strong></h5>
                        </div>
                        <div class="card-body">
                            <form class="form-horizontal" action="{{ route('product_detail_update') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-from-label">{{ translate('Choose CSV') }}</label>
                                            <div class="col-sm-9">
                                                <div class="custom-file">
                                                    <label class="custom-file-label">
                                                        <input type="file" name="bulk_file" class="custom-file-input"
                                                            required
                                                            accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel,text/comma-separated-values, text/csv, application/csv">
                                                        <span
                                                            class="custom-file-name">{{ translate('Choose File') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label
                                                class="col-md-4 col-from-label">{{ translate('Product update with barcode?') }}</label>
                                            <div class="col-md-8">
                                                <label class="aiz-switch aiz-switch-success mb-0">
                                                    <input type="checkbox" name="is_barcode">
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-0 text-right">
                                    <button type="submit" class="btn btn-primary">{{ translate('Upload CSV') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

@endsection
