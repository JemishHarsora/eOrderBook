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
                                <h1 class="h3">{{ translate('Edit Your Delivery Route') }}</h1>
                            </div>
                        </div>
                    </div>

                    <form class="" action="{{ route('routes.update', $route->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input name="_method" type="hidden" value="PUT">
                        <input type="hidden" name="created_by" value={{ Auth::user()->id }}>
                        <input type="hidden" name="id" value="{{ $route->id }}">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Route Information') }}</h5>
                            </div>
                            <div class="card-body">

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('City') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control aiz-selectpicker" name="city_id" id="city_id"
                                            data-live-search="true" required>
                                            <option value="">Select City</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}" @if ($route->city_id == $city->id) selected @endif>{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Area') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control aiz-selectpicker" name="area_id[]" id="area_id"
                                            data-live-search="true" required multiple>
                                            <option value="">Select Area</option>
                                            @foreach ($areas as $area)
                                                <option value="{{ $area->id }}" @if ($route->area_id == $area->id) selected @endif>{{ $area->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('User') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control aiz-selectpicker" name="user_id[]" multiple
                                            data-live-search="true" required>
                                            <option value="">Select Staff </option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" @if (in_array($user->id, explode(',', $route->user_id))) selected @endif>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Name') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control text-capitalize" name="name"
                                            placeholder="{{ translate('Name') }}" required value="{{ $route->name }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Date') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control aiz-selectpicker" name="date"
                                            data-selected={{ $route->day }} data-live-search="true" required>
                                            @for ($aaa = 1; $aaa <= 30; $aaa++)
                                                <option value="{{ $aaa }}">{{ $aaa }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="mar-all text-right">
                            <button type="submit" name="button"
                                class="btn btn-primary">{{ translate('Save Route') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            // getAreas();
            $('#city_id').on('change', function() {
                getAreas()
            });
        });

        function getAreas() {
            var city_id = jQuery("#city_id").val();
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
                    $('#area_id').html('<option value="">Select Area</option>');
                    $.each(result.states, function(key, value) {
                        $("#area_id").append('<option value="' + value.id + '">' + value.name +
                            '</option>');
                    });
                }
            });
        }

    </script>
@endsection
