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
                        <h1 class="h3">{{ translate('All uploaded files')}}</h1>
                    </div>
                  </div>
                </div>

                <div class="row gutters-10 justify-content-center">
                    <div class="col-md-4 mx-auto mb-3" >
                        <a href="{{ route('uploaded.create')}}">
                          <div class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition">
                              <span class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                                  <i class="las la-plus la-3x text-white"></i>
                              </span>
                              <div class="fs-18 text-primary">{{ translate('Upload New File') }}</div>
                          </div>
                        </a>
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

                </div>

                <div class="card">
                    <form id="sort_uploads" action="">
                        <div class="card-header row gutters-5">
                            <div class="col-md-3">
                                <h5 class="mb-0 h6">{{translate('All files')}}</h5>
                            </div>
                            <div class="col-md-3 ml-auto mr-0">
                                <select class="form-control form-control-xs aiz-selectpicker" name="sort" onchange="sort_uploads()">
                                    <option value="newest" @if($sort_by == 'newest') selected="" @endif>{{ translate('Sort by newest') }}</option>
                                    <option value="oldest" @if($sort_by == 'oldest') selected="" @endif>{{ translate('Sort by oldest') }}</option>
                                    <option value="smallest" @if($sort_by == 'smallest') selected="" @endif>{{ translate('Sort by smallest') }}</option>
                                    <option value="largest" @if($sort_by == 'largest') selected="" @endif>{{ translate('Sort by largest') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-xs" name="search" placeholder="{{ translate('Search your files') }}" value="{{ $search }}">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">{{ translate('Search') }}</button>
                            </div>
                        </div>
                    </form>
                    <div class="card-body">
                        <div class="row gutters-5">
                            @foreach($all_uploads as $key => $file)
                                @php
                                    if($file->file_original_name == null){
                                        $file_name = translate('Unknown');
                                    }else{
                                        $file_name = $file->file_original_name;
                                    }
                                @endphp
                                <div class="col-auto w-150px w-lg-220px">
                                    <div class="aiz-file-box">
                                        <div class="dropdown-file" >
                                            <a class="dropdown-link" data-toggle="dropdown">
                                                <i class="la la-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a href="javascript:void(0)" class="dropdown-item" onclick="detailsInfo(this)" data-id="{{ $file->id }}">
                                                    <i class="las la-info-circle mr-2"></i>
                                                    <span>{{ translate('Details Info') }}</span>
                                                </a>
                                                <a href="{{ my_asset($file->file_name) }}" target="_blank" download="{{ $file_name }}.{{ $file->extension }}" class="dropdown-item">
                                                    <i class="la la-download mr-2"></i>
                                                    <span>{{ translate('Download') }}</span>
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item" onclick="copyUrl(this)" data-url="{{ my_asset($file->file_name) }}">
                                                    <i class="las la-clipboard mr-2"></i>
                                                    <span>{{ translate('Copy Link') }}</span>
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item confirm-alert" data-href="{{ route('uploaded.destroy', $file->id ) }}" data-target="#delete-modal">
                                                    <i class="las la-trash mr-2"></i>
                                                    <span>{{ translate('Delete') }}</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card card-file aiz-uploader-select c-default" title="{{ $file_name }}.{{ $file->extension }}">
                                            <div class="card-file-thumb">
                                                @if($file->type == 'image')
                                                    <img src="{{ my_asset($file->file_name) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';" class="img-fit">
                                                @elseif($file->type == 'video')
                                                    <i class="las la-file-video"></i>
                                                @else
                                                    <i class="las la-file"></i>
                                                @endif
                                            </div>
                                            <div class="card-body">
                                                <h6 class="d-flex">
                                                    <span class="text-truncate title">{{ $file_name }}</span>
                                                    <span class="ext">.{{ $file->extension }}</span>
                                                    <span class="ext">({{ $file->id }})</span>
                                                </h6>
                                                <p>{{ formatBytes($file->file_size) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="aiz-pagination mt-3">
                            {{ $all_uploads->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('modal')
<div id="delete-modal" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mt-1">{{ translate('Are you sure to delete this file?') }}</p>
                <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{ translate('Cancel') }}</button>
                <a href="" class="btn btn-primary mt-2 comfirm-link">{{ translate('Delete') }}</a>
            </div>
        </div>
    </div>
</div>
<div id="info-modal" class="modal fade">
	<div class="modal-dialog modal-dialog-right">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title h6">{{ translate('File Info') }}</h5>
				<button type="button" class="close" data-dismiss="modal">
				</button>
			</div>
			<div class="modal-body c-scrollbar-light position-relative" id="info-modal-content">
				<div class="c-preloader text-center absolute-center">
                    <i class="las la-spinner la-spin la-3x opacity-70"></i>
                </div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('script')
	<script type="text/javascript">
		function detailsInfo(e){
            $('#info-modal-content').html('<div class="c-preloader text-center absolute-center"><i class="las la-spinner la-spin la-3x opacity-70"></i></div>');
			var id = $(e).data('id')
			$('#info-modal').modal('show');
			$.post('{{ route('uploaded.info') }}', {_token: AIZ.data.csrf, id:id}, function(data){
                $('#info-modal-content').html(data);
				// console.log(data);
			});
		}
		function copyUrl(e) {
			var url = $(e).data('url');
			var $temp = $("<input>");
		    $("body").append($temp);
		    $temp.val(url).select();
		    try {
			    document.execCommand("copy");
			    AIZ.plugins.notify('success', '{{ translate('Link copied to clipboard') }}');
			} catch (err) {
			    AIZ.plugins.notify('danger', '{{ translate('Oops, unable to copy') }}');
			}
		    $temp.remove();
		}
        function sort_uploads(el){
            $('#sort_uploads').submit();
        }
	</script>
@endsection
