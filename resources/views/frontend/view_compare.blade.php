@extends('frontend.layouts.app')

@section('content')

<section class="pt-4 mb-4">
    <div class="container-fluid text-center">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4">{{ translate('Compare')}}</h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="{{ route('home') }}">{{ translate('Home')}}</a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        <a class="text-reset" href="{{ route('compare.reset') }}">"{{ translate('Compare')}}"</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="mb-4">
    <div class="container-fluid text-left">
        <div class="bg-white shadow-sm rounded">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="fs-15 fw-600">{{ translate('Comparison')}}</div>
                <a href="{{ route('compare.reset') }}" style="text-decoration: none;" class="btn btn-soft-primary btn-sm fw-600">{{ translate('Reset Compare List')}}</a>
            </div>
            @if(Session::has('compare'))
                @if(count(Session::get('compare')) > 0)
                    <div class="p-3">
                        <table class="table table-responsive table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" style="width:16%" class="font-weight-bold">
                                        {{ translate('Name')}}
                                    </th>
                                    @foreach (Session::get('compare') as $key => $item)
                                        <th scope="col" style="width:28%" class="font-weight-bold">
                                            <a class="text-reset fs-15" href="{{ route('product', \App\ProductPrice::find($item)->slug) }}">
                                                @php $name = \App\ProductPrice::with(['product'])->find($item);
                                                   echo $name->product->getTranslation('name');
                                                @endphp
                                            </a>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">{{ translate('Image')}}</th>
                                    @foreach (Session::get('compare') as $key => $item)
                                        <td>
                                            @php $thumb = \App\ProductPrice::with(['product'])->find($item); @endphp
                                            <img loading="lazy" src="{{ uploaded_asset($thumb->product->thumbnail_img) }}" alt="{{ translate('Product Image') }}" class="img-fluid py-4"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                        </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th scope="row">{{ translate('Price')}}</th>
                                    @foreach (Session::get('compare') as $key => $item)
                                        <td>{{ single_price(\App\ProductPrice::find($item)->unit_price) }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th scope="row">{{ translate('Brand')}}</th>
                                    @foreach (Session::get('compare') as $key => $item)
                                        <td>
                                            @php $brand = \App\ProductPrice::with('product.brand')->find($item);
                                                if ($brand->product->brand != null)
                                                {
                                                    echo $brand->product->brand->getTranslation('name');
                                                }
                                            @endphp
                                        </td>
                                    @endforeach
                                </tr>
                               <tr>
                                    <th scope="row">{{ translate('Category')}}</th>
                                    @foreach (Session::get('compare') as $key => $item)
                                        <td>
                                            @php $category = \App\ProductPrice::with('product.category')->find($item);
                                                if ($category->product->category != null)
                                                {
                                                    echo $category->product->category->getTranslation('name');
                                                }
                                            @endphp
                                        </td>
                                    @endforeach
                                </tr>
                                {{-- <tr>
                                    <th scope="row">{{ translate('Sub Category')}}</th>
                                    @foreach (Session::get('compare') as $key => $item)
                                        <td>
                                            @php $subsubcategory = \App\ProductPrice::with('product','product.subsubcategory')->find($item);
                                                if ($subsubcategory->product->subsubcategory != null)
                                                {
                                                    echo $subsubcategory->product->subsubcategory->getTranslation('name');
                                                }
                                            @endphp
                                        </td>
                                    @endforeach
                                </tr> --}}

                                <tr>
                                    <th scope="row">{{ translate('Description')}}</th>
                                    @foreach (Session::get('compare') as $key => $item)
                                        <td>
                                            @php $description = \App\ProductPrice::with('product')->find($item);
                                                if ($description->product->description != null)
                                                {
                                                    echo $description->product->description;
                                                }
                                            @endphp
                                        </td>
                                    @endforeach
                                </tr>


                                <tr>
                                    <th scope="row"></th>
                                    @foreach (Session::get('compare') as $key => $item)
                                        <td class="text-center py-4">
                                            <button type="button" class="btn btn-primary fw-600" onclick="showAddToCartModal({{ $item }})">
                                                {{ translate('Add to cart')}}
                                            </button>
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            @else
                <div class="text-center p-4">
                    <p class="fs-17">{{ translate('Your comparison list is empty')}}</p>
                </div>
            @endif
        </div>
    </div>
</section>

@endsection
