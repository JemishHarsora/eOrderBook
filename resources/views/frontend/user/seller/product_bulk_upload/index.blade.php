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
                            <h1 class="h3">{{ translate('Bulk Products Upload') }}</h1>
                        </div>
                      </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <table class="table aiz-table mb-0" style="font-size:14px; background-color: #cce5ff; border-color: #b8daff">
                                <tr>
                                    <td>{{ translate('1. Download the skeleton file and fill it with data.')}}:</td>
                                </tr>
                                <tr >
                                    <td>{{ translate('2. You can download the example file to understand how the data must be filled.')}}:</td>
                                </tr>
                                <tr>
                                    <td>{{ translate('3. Once you have downloaded and filled the skeleton file, upload it in the form below and submit.')}}:</td>
                                </tr>
                                <tr>
                                    <td>{{ translate('4. After uploading products you need to edit them and set products choices.')}}</td>
                                </tr>
                            </table>
                            <a href="{{ static_asset('download/product_bulk_demo.xlsx') }}" download><button class="btn btn-primary mt-2">{{ translate('Download CSV') }}</button></a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="col text-center text-md-left">
                                <h5 class="mb-md-0 h6">{{ translate('Upload CSV File') }}</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <form class="form-horizontal" action="{{ route('bulk_product_upload') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row" id="category">
                                            <label class="col-md-3 col-from-label">{{translate('Category')}} <span class="text-danger">*</span></label>
                                            <div class="col-md-9">
                                                <select class="form-control aiz-selectpicker" name="category_id" id="category_id" data-live-search="true" required>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                                        @foreach ($category->childrenCategories as $childCategory)
                                                            @include('categories.child_category', ['child_category' => $childCategory])
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row" id="brand">
                                            <label class="col-md-3 col-from-label">{{translate('Brand')}}</label>
                                            <div class="col-md-9">
                                                <select class="form-control aiz-selectpicker" name="brand_id" id="brand_id" data-live-search="true">
                                                    <option value="">{{ ('Select Brand') }}</option>
                                                    @foreach ($brands as $brand)
                                                        <option value="{{ $brand->id }}">{{ $brand->getTranslation('name') }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-from-label">{{translate('CSV')}}</label>
                                            <div class="col-sm-9">
                                                <div class="custom-file">
                                                    <label class="custom-file-label">
                                                        <input type="file" name="bulk_file" class="custom-file-input" required accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel,text/comma-separated-values, text/csv, application/csv">
                                                        <span class="custom-file-name">{{ translate('Choose File')}}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-0 text-right">
                                    <button type="submit" class="btn btn-primary">{{translate('Upload CSV')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

@endsection
