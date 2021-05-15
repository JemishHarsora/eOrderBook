
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
                            <h1 class="h3">{{ translate('My Sellers') }}</h1>
                        </div>
                      </div>
                    </div>

                    <div class="card">
                        <div class="card-header row gutters-5">
                            <div class="col">
                                <h5 class="mb-md-0 h6">{{ translate('All Sellers') }}</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <form class="" action="" method="GET">
                                        <input type="text" class="form-control" id="search" name="search" @isset($search) value="{{ $search }}" @endisset placeholder="{{ translate('Search seller') }}">
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table aiz-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th data-breakpoints="md">{{ translate('Photo')}}</th>
                                        <th data-breakpoints="md">{{ translate('Shop Name')}}</th>
                                        <th data-breakpoints="md">{{ translate('Name')}}</th>
                                        <th data-breakpoints="md">{{ translate('E-mail')}}</th>
                                        <th data-breakpoints="md">{{ translate('Address')}}</th>
                                        <th data-breakpoints="md">{{ translate('Phone')}}</th>
                                        <th data-breakpoints="md">{{ translate('Status')}}</th>
                                        <th data-breakpoints="md" class="text-right">{{ translate('Options')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $key => $value)
                                        <tr>
                                            <td>{{ ($key+1) + ($customers->currentPage() - 1)*$customers->perPage() }}</td>
                                            <td>
                                                <img src="{{ my_asset($value->avatar) }}" class="w-50px"
                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                                            </td>
                                            <td>{{ $value->shop_name }}</td>
                                            <td>{{ $value->name }}</td>
                                            <td>{{ $value->email }}</td>
                                            <td>{{ $value->address }}</td>
                                            <td>{{ $value->phone }}</td>

                                            <td><label class="aiz-switch aiz-switch-success mb-0">
                                                <input onchange="updateStatus(this)" value="{{ $value->cust_id }}" type="checkbox" <?php if($value->status == 1) echo "checked";?> >
                                                <span class="slider round"></span></label>
                                            </td>
                                            <td class="text-right">
                		                      <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="{{route('seller.staffs.edit', ['id'=>$value->id, 'lang'=>env('DEFAULT_LANGUAGE')])}}" title="{{ translate('Edit') }}">
                		                          <i class="las la-edit"></i>
                		                      </a>

                                              <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('products.destroy', $value->id)}}" title="{{ translate('Delete') }}">
                                                  <i class="las la-trash"></i>
                                              </a>
                                          </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="aiz-pagination">
                                {{ $customers->links() }}
                          	</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
@section('script')
    <script type="text/javascript">
        function updateStatus(el){
            if(el.checked){

                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('mysellers.changeStatus') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Seller status updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection
