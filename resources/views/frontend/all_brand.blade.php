@extends('frontend.layouts.app')

@section('content')

<section class="pt-4 mb-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4">{{ translate('All Brands') }}</h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="{{ route('home') }}">{{ translate('Home')}}</a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        <a class="text-reset" href="{{ route('brands.all') }}">"{{ translate('All Brands') }}"</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section class="mb-4">
    <div class="container-fluid">
        <div class="bg-white shadow-sm rounded px-3 pt-3">
            <div class="row row-cols-xxl-6 row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-2 gutters-10">
                @php
                $area_seller = getAreaWiseBrand();

                $brands= \App\Brand::all();
                if($area_seller['seller_ids'] !=''){
                    if($area_seller['seller_ids']['0'] != null){
                    $brands= \App\Brand::whereIn('id', $area_seller->brand_ids)->get();
                    }else{
                        $brands= \App\Brand::where('id', $area_seller['brand_ids']['0'])->get();
                    }
                }
                @endphp

                @foreach ($brands as $brand)
                    <div class="col text-center">
                        <a href="{{ route('products.brand', $brand->slug) }}" class="d-block p-3 mb-3 border border-light rounded hov-shadow-md">
                            <img src="{{ uploaded_asset($brand->logo) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';" class="lazyload mx-auto h-70px mw-100" alt="{{ $brand->getTranslation('name') }}">
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@endsection
