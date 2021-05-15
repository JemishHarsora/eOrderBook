@extends('backend.layouts.app')

@section('content')

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Customer Information') }}</h5>
            <div class="pull-right clearfix">
                {{-- <a href="{{ route('customer.reject', $customer->id) }}"
                    class="btn btn-default d-innline-block">{{ translate('Reject') }}</a></li>
                <a href="{{ route('customer.approve', $customer->id) }}"
                    class="btn btn-circle btn-dark d-innline-block">{{ translate('Accept') }}</a> --}}
                <a href="#" onclick="history.go(-1)"
                    class="btn btn-circle btn-dark d-innline-block">{{ translate('Back') }}</a>
            </div>
        </div>
        <div class="card-body row">
            <div class="col-md-5">
                <h6 class="mb-4">{{ translate('User Info') }}</h6>
                <p class="text-muted">
                    <strong>{{ translate('Name') }} :</strong>
                    <span class="ml-2">{{ $customer->user->name }}</span>
                </p>
                <p class="text-muted">
                    <strong>{{ translate('Email') }}</strong>
                    <span class="ml-2">{{ $customer->user->email }}</span>
                </p>
                <p class="text-muted">
                    <strong>{{ translate('Address') }}</strong>
                    <span class="ml-2">{{ $customer->user->address }}</span>
                </p>
                <p class="text-muted">
                    <strong>{{ translate('Phone') }}</strong>
                    <span class="ml-2">{{ $customer->user->phone }}</span>
                </p>
                <br>

                <h6 class="mb-4">{{ translate('Shop Info') }}</h6>
                <p class="text-muted">
                    <strong>{{ translate('Shop Name') }}</strong>
                    <span class="ml-2">{{ $customer->user->customerShop->name }}</span>
                </p>
                <p class="text-muted">
                    <strong>{{ translate('Address') }}</strong>
                    <span class="ml-2">{{ $customer->user->address }}</span>
                </p>
            </div>
            <div class="col-md-5">
                <h6 class="mb-4">{{ translate('Verification Info') }}</h6>
                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <tbody>
                        @foreach (json_decode($customer->verification_info) as $key => $info)
                            <tr>
                                <th class="text-muted">{{ $info->label }}</th>
                                @if ($info->type == 'text')
                                    <td>{{ $info->value }}</td>

                                @elseif ($info->type == 'file')
                                    <td>
                                        @if ($info->value != '')
                                            <a href="{{ my_asset($info->value) }}" target="_blank"
                                                class="btn-info">{{ translate('Click here') }}</a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- <div class="text-center">
                <a href="{{ route('customer.reject', $customer->id) }}"
                    class="btn btn-sm btn-default d-innline-block">{{translate('Reject')}}</a></li>
                <a href="{{ route('customer.approve', $customer->id) }}"
                    class="btn btn-sm btn-dark d-innline-block">{{translate('Accept')}}</a>
            </div> --}}
            </div>
        </div>
    </div>

@endsection
