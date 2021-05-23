
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
                            <h1 class="h3">{{ translate('Staffs') }}</h1>
                        </div>
                      </div>
                    </div>

                    <div class="row gutters-10 justify-content-center">
                        @if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated)
                            <div class="col-md-4 mx-auto mb-3" >
                                <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                                <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                                    <i class="las la-upload la-2x text-white"></i>
                                </span>
                                <div class="px-3 pt-3 pb-3">
                                    <div class="h4 fw-700 text-center">{{ max(0, Auth::user()->seller->remaining_uploads) }}</div>
                                    <div class="opacity-50 text-center">{{  translate('Remaining Uploads') }}</div>
                                </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4 mx-auto mb-3" >
                            <a href="{{ route('seller.staffs.upload')}}">
                              <div class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition">
                                  <span class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                                      <i class="las la-plus la-3x text-white"></i>
                                  </span>
                                  <div class="fs-18 text-primary">{{ translate('Add New Staff') }}</div>
                              </div>
                            </a>
                        </div>

                    </div>

                    @if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated)
                    @php
                        $seller_package = \App\SellerPackage::find(Auth::user()->seller->seller_package_id);
                    @endphp
                    <div class="col-md-4">
                        <a href="{{ route('seller_packages_list') }}" class="text-center bg-white shadow-sm hov-shadow-lg text-center d-block p-3 rounded">
                            @if($seller_package != null)
                                <img src="{{ uploaded_asset($seller_package->logo) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';" height="44" class="mw-100 mx-auto">
                                <span class="d-block sub-title mb-2">{{ translate('Current Package')}}: {{ $seller_package->getTranslation('name') }}</span>
                            @else
                                <i class="la la-frown-o mb-2 la-3x"></i>
                                <div class="d-block sub-title mb-2">{{ translate('No Package Found')}}</div>
                            @endif
                            <div class="btn btn-outline-primary py-1">{{ translate('Upgrade Package')}}</div>
                        </a>
                    </div>
                    @endif


                    <div class="card">
                        <div class="card-header row gutters-5">
                            <div class="col">
                                <h5 class="mb-md-0 h6">{{ translate('All Staffs') }}</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <form class="" action="" method="GET">
                                        <input type="text" class="form-control" id="search" name="search" @isset($search) value="{{ $search }}" @endisset placeholder="{{ translate('Search staff') }}">
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table aiz-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th data-breakpoints="md">{{ translate('Name')}}</th>
                                        <th data-breakpoints="md">{{ translate('email')}}</th>
                                        <th data-breakpoints="md">{{ translate('Phone')}}</th>
                                        <th>{{ translate('Role')}}</th>
                                        <th data-breakpoints="md" class="text-right">{{ translate('Options')}}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($staffs as $key => $staff)
                                        <tr>
                                            <td>{{ ($key+1) + ($staffs->currentPage() - 1)*$staffs->perPage() }}</td>
                                            <td>{{ $staff->name }}</td>
                                            <td>{{ $staff->email }}</td>
                                            <td>{{ $staff->phone }}</td>
                                            <td>{{ $staff->user_type }}</td>

                                            <td class="text-right">
                		                      <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="{{route('seller.staffs.edit', ['id'=>$staff->id, 'lang'=>env('DEFAULT_LANGUAGE')])}}" title="{{ translate('Edit') }}">
                		                          <i class="las la-edit"></i>
                		                      </a>

                                              <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('products.destroy', $staff->id)}}" title="{{ translate('Delete') }}">
                                                  <i class="las la-trash"></i>
                                              </a>
                                          </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="aiz-pagination">
                                {{ $staffs->links() }}
                          	</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
