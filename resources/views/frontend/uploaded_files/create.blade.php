@extends('frontend.layouts.app')

@section('content')

    <section class="py-5">
        <div class="container-fluid">
            <div class="d-flex align-items-start">
                @include('frontend.inc.user_side_nav')

                <div class="aiz-user-panel">

                    <div class="aiz-titlebar mt-2 mb-2">
                      <div class="row align-items-center">
                        <div class="col-md-6">
                            <h1 class="h3">{{ translate('Upload New File') }}</h1>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <a href="{{ route('uploaded.index') }}" class="btn btn-link text-reset">
                                <i class="las la-angle-left"></i>
                                <span>{{translate('Back to uploaded files')}}</span>
                            </a>
                        </div>
                      </div>
                    </div>
                    
                    {{-- <div class="aiz-titlebar text-left mt-2 mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h1 class="h3">{{translate('Upload New File')}}</h1>
                            </div>

                        </div>
                    </div> --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Drag & drop your files')}}</h5>
                        </div>
                        <div class="card-body">
                            <div id="aiz-upload-files" class="h-420px" style="min-height: 65vh">

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
		$(document).ready(function() {
			AIZ.plugins.aizUppy();
		});
	</script>
@endsection
