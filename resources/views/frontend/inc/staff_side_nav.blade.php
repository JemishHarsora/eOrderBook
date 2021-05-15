<div class="aiz-user-sidenav-wrap pt-4 position-relative z-1 shadow-sm">
    <div class="absolute-top-right d-xl-none">
        <button class="btn btn-sm p-2" data-toggle="class-toggle" data-target=".aiz-mobile-side-nav"
            data-same=".mobile-side-nav-thumb">
            <i class="las la-times la-2x"></i>
        </button>
    </div>
    <div class="absolute-top-left d-xl-none">
        <a class="btn btn-sm p-2" href="{{ route('logout') }}">
            <i class="las la-sign-out-alt la-2x"></i>
        </a>
    </div>
    <div class="aiz-user-sidenav rounded overflow-hidden  c-scrollbar-light">
        <div class="px-4 text-center mb-4">
            <span class="avatar avatar-md mb-3">
                @if (Auth::user()->avatar_original != null)
                    <img src="{{ uploaded_asset(Auth::user()->avatar_original) }}"
                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                @else
                    <img src="{{ static_asset('assets/img/avatar-place.png') }}" class="image rounded-circle"
                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                @endif
            </span>

            @if (Auth::user()->user_type == 'salesman' || Auth::user()->user_type == 'delivery')
                <h4 class="h5 fw-600">{{ Auth::user()->name }}
                    <span class="ml-2">
                        <i class="las la-check-circle" style="color:green"></i>
                    </span>
                </h4>
            @endif
        </div>

        <div class="sidemnenu mb-3">
            <ul class="aiz-side-nav-list" data-toggle="aiz-side-menu">

                <li class="aiz-side-nav-item">
                    <a href="{{ route('selerStaffDashboard') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['dashboard']) }}">
                        <i class="las la-home aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Dashboard') }}</span>
                    </a>
                </li>
                @if (Auth::user()->user_type == 'salesman')
                    @php
                        $orders = DB::table('orders')
                            ->orderBy('code', 'desc')
                            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                            ->where('order_details.seller_id', Auth::user()->created_by)
                            ->where('orders.viewed', 0)
                            ->select('orders.id')
                            ->distinct()
                            ->count();
                    @endphp
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('orders.index') }}"
                            class="aiz-side-nav-link {{ areActiveRoutes(['orders.index']) }}">
                            <i class="las la-money-bill aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Orders') }}</span>
                            @if ($orders > 0)<span
                                    class="badge badge-inline badge-success">{{ $orders }}</span>@endif
                        </a>
                    </li>
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('orders.create') }}"
                            class="aiz-side-nav-link {{ areActiveRoutes(['orders.create']) }}">
                            <i class="las la-user aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Add Order') }}</span>
                        </a>
                    </li>
                @endif
                <li class="aiz-side-nav-item">
                    <a href="{{ route('staffProfile') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['profile']) }}">
                        <i class="las la-user aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Manage Profile') }}</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
