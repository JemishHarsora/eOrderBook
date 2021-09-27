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
                                <h1 class="h3">{{ translate('Delivery Routes') }}</h1>
                            </div>
                        </div>
                    </div>

                    <div class="row gutters-10 justify-content-center">
                        <div class="col-md-4 mx-auto mb-3">
                            <a href="{{ route('routes.create') }}">
                                <div
                                    class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition">
                                    <span
                                        class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                                        <i class="las la-plus la-3x text-white"></i>
                                    </span>
                                    <div class="fs-18 text-primary">{{ translate('Add New Route') }}</div>
                                </div>
                            </a>
                        </div>

                        @if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated)
                            @php
                                $seller_package = \App\SellerPackage::find(Auth::user()->seller->seller_package_id);
                            @endphp
                            <div class="col-md-4">
                                <a href="{{ route('seller_packages_list') }}"
                                    class="text-center bg-white shadow-sm hov-shadow-lg text-center d-block p-3 rounded">
                                    @if ($seller_package != null)
                                        <img src="{{ uploaded_asset($seller_package->logo) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';" height="44"
                                            class="mw-100 mx-auto">
                                        <span class="d-block sub-title mb-2">{{ translate('Current Package') }}:
                                            {{ $seller_package->getTranslation('name') }}</span>
                                    @else
                                        <i class="la la-frown-o mb-2 la-3x"></i>
                                        <div class="d-block sub-title mb-2">{{ translate('No Package Found') }}</div>
                                    @endif
                                    <div class="btn btn-outline-primary py-1">{{ translate('Upgrade Package') }}</div>
                                </a>
                            </div>
                        @endif

                    </div>

                    <div class="card">
                        <div class="card-header row gutters-5">
                            <div class="col">
                                <h5 class="mb-md-0 h6">{{ translate('All Routes') }}</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <form class="" action="" method="GET">
                                        <input type="text" class="form-control" id="search" name="search" @isset($search)
                                            value="{{ $search }}" @endisset
                                            placeholder="{{ translate('Search route') }}">
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table aiz-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ translate('Name') }}</th>
                                        {{-- <th data-breakpoints="md">{{ translate('Staff Name') }}</th> --}}
                                        <th data-breakpoints="md">{{ translate('Date') }}</th>
                                        <th data-breakpoints="md">{{ translate('Area') }}</th>
                                        <th data-breakpoints="md">{{ translate('City') }}</th>
                                        <th data-breakpoints="md" class="text-right">{{ translate('Options') }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($routes as $key => $route)
                                        <tr>
                                            <td>{{ $key + 1 + ($routes->currentPage() - 1) * $routes->perPage() }}</td>
                                            <td>{{ $route->name }}</td>
                                            {{-- <td>
                                                {{ \App\User::whereIn('id', explode(',', $route->user_id))->pluck('name')->implode(',') }}
                                            </td> --}}
                                            <td>{{ $route->day }}</td>
                                            <td>
                                                @if ($route->area_id != null)
                                                    {{ \App\Area::whereIn('id', explode(',', $route->area_id))->pluck('name')->implode(',') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($route->city != null)
                                                    {{ $route->city->name }}
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <a class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                                    href="{{ route('routes.edit', ['id' => $route->id, 'lang' => env('DEFAULT_LANGUAGE')]) }}"
                                                    title="{{ translate('Edit') }}">
                                                    <i class="las la-edit"></i>
                                                </a>
                                                <a href="#"
                                                    class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                                    data-href="{{ route('routes.destroy', $route->id) }}"
                                                    title="{{ translate('Delete') }}">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="aiz-pagination">
                                {{ $routes->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    {{-- <script type="text/javascript">
        function update_featured(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.seller.featured') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Featured products updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_published(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.published') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Published products updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script> --}}
@endsection
