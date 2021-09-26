@extends('frontend.layouts.app')

@section('content')

<section class="py-5">
    {{-- {{dd($shop)}} --}}
    <div class="container-fluid">
        <div class="d-flex align-items-start">
            @include('frontend.inc.user_side_nav')

            <div class="aiz-user-panel">

                <div class="aiz-titlebar mt-2 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h1 class="h3">{{ translate('Shop Settings')}}
                                {{-- <a href="{{ route('shop.visit', $shop->slug) }}" class="btn btn-link btn-sm"
                                target="_blank">({{ translate('Visit Shop')}})<i class="la la-external-link"></i>)</a>
                                --}}
                            </h1>
                        </div>
                    </div>
                </div>

                {{-- Basic Info --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Basic Info') }}</h5>
                    </div>
                    <div class="card-body">
                        <form class="" action="{{ route('customerShops.update', $shop->id) }}" method="POST"
                            enctype="multipart/form-data">
                            <input type="hidden" name="_method" value="PATCH">
                            @csrf
                            <div class="row">
                                <label class="col-md-2 col-form-label">{{ translate('Shop Name') }}<span
                                        class="text-danger text-danger">*</span></label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3"
                                        placeholder="{{ translate('Shop Name')}}" name="name" value="{{ $shop->name }}"
                                        required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-2 col-form-label">{{ translate('Shop Logo') }}</label>
                                <div class="col-md-10">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                {{ translate('Browse')}}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="logo" value="{{ $shop->logo }}"
                                            class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-2 col-form-label">{{ translate('Shop Address') }} <span
                                        class="text-danger text-danger">*</span></label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3" placeholder="{{ translate('Address')}}"
                                        name="address" value="{{ Auth::user()->address }}" required>
                                </div>
                            </div>
                            @if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value ==
                            'seller_wise_shipping')
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Shipping Cost')}} <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-10">
                                    <input type="number" lang="en" min="0" class="form-control mb-3"
                                        placeholder="{{ translate('Shipping Cost')}}" name="shipping_cost"
                                        value="{{ $shop->shipping_cost }}" required>
                                </div>
                            </div>
                            @endif
                            @if (\App\BusinessSetting::where('type', 'pickup_point')->first()->value == 1)
                            <div class="row mb-3">
                                <label class="col-md-2 col-form-label">{{ translate('Pickup Points') }}</label>
                                <div class="col-md-10">
                                    <select class="form-control aiz-selectpicker"
                                        data-placeholder="{{ translate('Select Pickup Point') }}" id="pick_up_point"
                                        name="pick_up_point_id[]" multiple>
                                        @foreach (\App\PickupPoint::all() as $pick_up_point)
                                        @if (Auth::user()->shop->pick_up_point_id != null)
                                        <option value="{{ $pick_up_point->id }}" @if (in_array($pick_up_point->id,
                                            json_decode(Auth::user()->shop->pick_up_point_id))) selected
                                            @endif>{{ $pick_up_point->getTranslation('name') }}</option>
                                        @else
                                        <option value="{{ $pick_up_point->id }}">
                                            {{ $pick_up_point->getTranslation('name') }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif

                            <div class="row">
                                <label class="col-md-2 col-form-label">{{ translate('Meta Title') }}<span
                                        class="text-danger text-danger">*</span></label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3"
                                        placeholder="{{ translate('Meta Title')}}" name="meta_title"
                                        value="{{ $shop->meta_title }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-2 col-form-label">{{ translate('Meta Description') }}<span
                                        class="text-danger text-danger">*</span></label>
                                <div class="col-md-10">
                                    <textarea name="meta_description" rows="3" class="form-control mb-3"
                                        required>{{ $shop->meta_description }}</textarea>
                                </div>
                            </div>
                            <div class="form-group mb-0 text-right">
                                <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>



            </div>
        </div>
    </div>
</section>

@endsection
