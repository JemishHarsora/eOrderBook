@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('area Information')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-body p-0">
            {{-- <ul class="nav nav-tabs nav-fill border-light">
  				@foreach (\App\Language::all() as $key => $language)
  					<li class="nav-item">
  						<a class="nav-link text-reset @if ($language->code == $lang) active @else bg-soft-dark border-light border-left-0 @endif py-3" href="{{ route('areas.edit', ['id'=>$area->id, 'lang'=> $language->code] ) }}">
  							<img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';" height="11" class="mr-1">
  							<span>{{ $language->name }}</span>
  						</a>
  					</li>
	            @endforeach
  			</ul> --}}
            <form class="p-4" action="{{ route('areas.update', $area->id) }}" method="POST" enctype="multipart/form-data">
                <input name="_method" type="hidden" value="PATCH">
                <input type="hidden" name="lang" value="{{ $lang }}">
                @csrf
                <div class="form-group mb-3">
                    <label for="name">{{translate('Name')}}</label>
                    <input type="text" placeholder="{{translate('Name')}}" value="{{ $area->name }}" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="city">{{translate('City')}}</label>
                    <select class="select2 form-control aiz-selectpicker" name="city_id" data-toggle="select2" data-placeholder="Choose ..." data-live-search="true">
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" @if($city->id == $area->city_id) selected @endif>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
