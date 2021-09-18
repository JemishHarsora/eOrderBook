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
                                <h1 class="h3">{{ translate('Edit Your Staff') }}</h1>
                            </div>
                        </div>
                    </div>

                    <form class="" action="{{ route('seller.staffs.update', $staff->id) }}" method="POST"
                        enctype="multipart/form-data" id="choice_form">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Staff Information') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Name') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control text-capitalize" name="name"
                                            placeholder="{{ translate('Name') }}" required value="{{ $staff->name }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Email') }}</label>
                                    <div class="col-md-8">
                                        <input type="Email" class="form-control" name="email"
                                            placeholder="{{ translate('Email') }}" required value="{{ $staff->email }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Phone') }}</label>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control" name="phone"
                                            placeholder="{{ translate('Phone') }}" required value="{{ $staff->phone }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('password') }}</label>
                                    <div class="col-md-8">
                                        <input type="password" class="form-control" name="password"
                                            placeholder="{{ translate('password') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Role') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control aiz-selectpicker" name="user_type" id="user_type"
                                            required>
                                            <option value="salesman" @if ($staff->user_type == 'salesman') {{
                                                    'selected'}} @endif
                                            } @endphp>Salesman</option>
                                            <option value="delivery" @if ($staff->user_type == 'delivery') {{
                                                    'selected'}} @endif
                                            } @endphp>Delivery boy </option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mar-all text-right">
                            <button type="submit" name="button"
                                class="btn btn-primary">{{ translate('Save Staff') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection
