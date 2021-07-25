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
                                <h1 class="h3">{{ translate('Edit Your Brand') }}</h1>
                            </div>
                        </div>
                    </div>


                    <form class="" action="{{ route('myBrands.update', $seller->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input name="_method" type="hidden" value="PUT">
                        <input type="hidden" name="created_by" value={{ Auth::user()->id }}>
                        <input type="hidden" name="id" value="{{ $seller->id }}">
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
                                                <option value="{{ $brand->id }}" @if ($seller->brand_id == $brand->id) selected @endif>{{ $brand->name }}</option>
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
                                                <option value="{{ $city->id }}" @if ($seller->city_id == $city->id) selected @endif>{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Area') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control" name="area_id[]" id="area_id" multiple required>
                                            @foreach ($areas as $area)
                                                <option value="{{ $area->id }}" @if (in_array($area->id, $area_id)) selected @endif>{{ $area->name }}</option>
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
