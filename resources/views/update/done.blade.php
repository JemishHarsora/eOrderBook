@extends('backend.layouts.blank')
@section('content')
    <div class="container-fluid pt-5">
        <div class="row">
            <div class="col-xl-6 mx-auto">
                <div class="card">
                    <div class="card-body">
					    <div class="mar-ver pad-btm text-center">
					        <h1 class="h3">Congratulations</h1>
					        <p>You have successfully completed the updating process. Please Login to continue</p>
					    </div>
					    <div class="text-center">
					        <a href="{{ env('APP_URL') }}" class="btn btn-primary">Go to Home</a>
					        <a href="{{ env('APP_URL') }}/admin" class="btn btn-success">Login to Admin panel</a>
					    </div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
