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
                            <h1 class="h3">{{ translate('Dashboard') }}</h1>
                        </div>
                    </div>
                </div>
                <div class="row gutters-10">
            		<div class="col-md-4">
            			<div class="bg-grad-2 text-white rounded-lg mb-4 overflow-hidden">
            				<div class="px-3 pt-3">
                                @php
                                    $orders = \App\Order::where('user_id', Auth::user()->id)->get();
                                    $total = 0;
                                    foreach ($orders as $key => $order) {
                                        $total += count($order->orderDetails);
                                    }
                                @endphp
              					<div class="h3 fw-700">{{ count(Auth::user()->wishlists)}} {{ translate('Product(s)') }}</div>
              					<div class="opacity-50">{{ translate('in your wishlist') }}</div>
			                </div>
        					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
          						<path fill="rgba(255,255,255,0.3)" fill-opacity="1" d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
					         </svg>
            			</div>
            		</div>
            		<div class="col-md-4">
            			<div class="bg-grad-3 text-white rounded-lg mb-4 overflow-hidden">
            				<div class="px-3 pt-3">
                                @php
                                    $orders = \App\Order::where('user_id', Auth::user()->id)->get();
                                    $total = 0;
                                    foreach ($orders as $key => $order) {
                                        $total += count($order->orderDetails);
                                    }
                                @endphp
	        					<div class="h3 fw-700">{{ $total }} {{ translate('Product(s)') }}</div>
	        					<div class="opacity-50">{{ translate('you ordered') }}</div>
				            </div>
          					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
    							<path fill="rgba(255,255,255,0.3)" fill-opacity="1" d="M0,192L26.7,192C53.3,192,107,192,160,202.7C213.3,213,267,235,320,218.7C373.3,203,427,149,480,117.3C533.3,85,587,75,640,90.7C693.3,107,747,149,800,149.3C853.3,149,907,107,960,112C1013.3,117,1067,171,1120,202.7C1173.3,235,1227,245,1280,213.3C1333.3,181,1387,107,1413,69.3L1440,32L1440,320L1413.3,320C1386.7,320,1333,320,1280,320C1226.7,320,1173,320,1120,320C1066.7,320,1013,320,960,320C906.7,320,853,320,800,320C746.7,320,693,320,640,320C586.7,320,533,320,480,320C426.7,320,373,320,320,320C266.7,320,213,320,160,320C106.7,320,53,320,27,320L0,320Z"></path>
    						</svg>
            			</div>
            		</div>
            	</div>
              <div class="row gutters-10">
            		<div class="col-md-6">
            			<div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">{{ translate('Default Shipping Address') }}</h6>
                            </div>
    				        <div class="card-body">
                              @if(Auth::user()->addresses != null)
                                  @php
                                      $address = Auth::user()->addresses->where('set_default', 1)->first();
                                  @endphp
                                  @if($address != null)
                                      <ul class="list-unstyled mb-0">
    					                  <li class=" py-2"><span>{{ translate('Address') }} : {{ $address->address }}</span></li>
                                          <li class=" py-2"><span>{{ translate('Country') }} : {{ $address->country }}</span></li>
                                          <li class=" py-2"><span>{{ translate('City') }} : {{ $address->city }}</span></li>
                                          <li class=" py-2"><span>{{ translate('Postal Code') }} : {{ $address->postal_code }}</span></li>
                                          <li class=" py-2"><span>{{ translate('Phone') }} : {{ $address->phone }}</span></li>
                                      </ul>
                                  @endif
                              @endif
            				</div>
            			</div>
                    </div>

                    @if(isset($routes) && !empty($routes))

                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Routes</h6>
                            </div>
                            <div class="card-body">
                                <table class="table aiz-table mb-0 footable footable-2 breakpoint-lg" style="">
                                    <thead>
                                        <tr>
                                            <th>Today's Route</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($routes as $route)
                                        <tr>
                                            <td class="footable-first-visible" style="display: table-cell;"><span>{{ $route->name }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- @endif --}}
                        </div>
                    </div>
                @endif
                    @if (\App\BusinessSetting::where('type', 'classified_product')->first()->value)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ translate('Purchased Package') }}</h6>
                                </div>
                                <div class="card-body text-center">
                                    @php
                                        $customer_package = \App\CustomerPackage::find(Auth::user()->customer_package_id);
                                    @endphp
                                    @if($customer_package != null)
                                        <img src="{{ uploaded_asset($customer_package->logo) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';" class="img-fluid mb-4 h-110px">
                                		<p class="mb-1 text-muted">{{ translate('Product Upload') }}: {{ $customer_package->product_upload }} {{ translate('Times')}}</p>
                                		<p class="text-muted mb-4">{{ translate('Product Upload Remaining') }}: {{ Auth::user()->remaining_uploads }} {{ translate('Times')}}</p>
                                        <h5 class="fw-600 mb-3 text-primary">{{ translate('Current Package') }}: {{ $customer_package->getTranslation('name') }}</h5>
                                    @else
                                        <h5 class="fw-600 mb-3 text-primary">{{translate('Package Not Found')}}</h5>
                                    @endif
                                    <a href="{{ route('customer_packages_list_show') }}" class="btn btn-success d-inline-block">{{ translate('Upgrade Package') }}</a>
                                </div>
                            </div>
                        </div>
                    @endif
            	</div>
            </div>
        </div>
    </div>
</section>
@endsection
