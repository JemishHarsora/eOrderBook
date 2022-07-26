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
                                <h1 class="h3">{{ translate('Add Your Delivery Route') }}</h1>
                            </div>
                        </div>
                    </div>

                    <form class="" action="{{ route('routes.store') }}" method="POST" enctype="multipart/form-data"
                        id="choice_form">
                        @csrf
                        <input type="hidden" name="created_by" value={{ Auth::user()->id }}>
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
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Area') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control aiz-selectpicker" name="area_id[]" id="area_id" multiple
                                            data-live-search="true" required>

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
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Name (day)') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control text-capitalize" name="name"
                                            placeholder="{{ translate('Name') }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Date') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control aiz-selectpicker" multiple name="date[]" data-live-search="true"
                                            required>
                                            @for ($aaa = 1; $aaa <= 31; $aaa++)
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
                        $('#area_id').html('<option value="">Select Area</option>');
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
