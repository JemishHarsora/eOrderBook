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
                                <h1 class="h3">{{ translate('Add Your Staff') }}</h1>
                            </div>
                        </div>
                    </div>

                    <form class="" action="{{ route('seller.staffs.store') }}" method="POST" enctype="multipart/form-data" id="choice_form">
                        @csrf
                        <input type="hidden" name="created_by" value={{ Auth::user()->id }}>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Staff Information') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Name') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control text-capitalize" name="name" placeholder="{{ translate('Name') }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Email') }}</label>
                                    <div class="col-md-8">
                                        <input type="Email" class="form-control" name="email" placeholder="{{ translate('Email') }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Phone') }}</label>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control" name="phone" placeholder="{{ translate('Phone') }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('password') }}</label>
                                    <div class="col-md-8">
                                        <input type="password" class="form-control" name="password" placeholder="{{ translate('password') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Role') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control aiz-selectpicker" name="user_type" id="user_type" required>
                                            <option value="sales">Salesman</option>
                                            <option value="delivery">Delivery boy </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mar-all text-right">
                            <button type="submit" name="button" class="btn btn-primary">{{ translate('Save Staff') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection
