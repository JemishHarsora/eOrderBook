@extends('frontend.layouts.app')

@section('content')

    <section class="py-5">
        <div class="container-flude">
            <div class="d-flex align-items-start">
                @include('frontend.inc.user_side_nav')

                <div class="aiz-user-panel">

                    <div class="aiz-titlebar mt-2 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h1 class="h3">{{ translate('Add Your New Brand') }}</h1>
                            </div>
                            <div class="col-md-12 text-md-right">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addBrand">
                                    {{ translate('Add New?') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <form class="" action="{{ route('myBrands.store') }}" method="POST" enctype="multipart/form-data"
                        id="choice_form">
                        @csrf
                        <input type="hidden" name="created_by" value={{ Auth::user()->id }}>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Brand Information') }}</h5>
                            </div>
                            <div class="card-body">

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Brand') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control aiz-selectpicker" name="brand_id"
                                            data-live-search="true" required>
                                            <option value="">Select Brand</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('City') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control aiz-selectpicker" name="city_id" id="city_id"
                                            data-live-search="true" required>
                                            <option value="">Select City</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}" @if (Auth::user()->city == $city->id) selected @endif>{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Area') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control" name="area_id[]" id="area_id" multiple required>
                                            @foreach ($areas as $area)
                                                <option value="{{ $area->id }}">{{ $area->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mar-all text-right">
                            <button type="submit" name="button"
                                class="btn btn-primary">{{ translate('Save Brand') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="addBrand" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Add New Brand</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('brand.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name">{{ translate('Name') }}</label>
                            <input type="text" placeholder="{{ translate('Name') }}" name="name"
                                class="form-control text-capitalize" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">{{ translate('Logo') }}
                                <small>({{ translate('120x80') }})</small></label>
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="logo" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">{{ translate('Meta Title') }}</label>
                            <input type="text" class="form-control text-capitalize" name="meta_title"
                                placeholder="{{ translate('Meta Title') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">{{ translate('Meta Description') }}</label>
                            <textarea name="meta_description" rows="5" class="form-control"></textarea>
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

@section('script')
    <script src="{{ static_asset('assets/js/jquery.multiselect.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            $('#area_id').multiselect({
                columns: 2,
                search: true,
                selectAll: true
            });

            $('#city_id').on('change', function() {
                var city_id = this.value;
                $("#area_id").html('');
                $.ajax({
                    url: "{{ url('seller/routes/getareas') }}",
                    type: "POST",
                    data: {
                        city_id: city_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $.each(result.states, function(key, value) {
                            $("#area_id").append('<option value="' + value.id + '">' +
                                value.name + '</option>');
                        });
                    }
                });
            });
        });

    </script>
@endsection
