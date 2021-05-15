
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
                            <h1 class="h3">{{ translate('All Request') }}</h1>
                        </div>
                      </div>
                    </div>

                    <div class="card">
                        <div class="card-header row gutters-5">
                            <h5 class="mb-0 h6">{{translate('Block Users')}}</h5>
                        </div>
                        <div class="card-body">
                            <table class="table aiz-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{translate('Name')}}</th>
                                        <th>{{translate('Blocker Name')}}</th>
                                        <th>{{translate('Reason')}}</th>

                                        <th>{{translate('Options')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($blockUsers as $key => $value)
                                        @if($value != null)
                                            <tr>
                                                <td>
                                                    {{ $key+1 }}
                                                </td>
                                                <td>
                                                    @if ($value->name != null)
                                                        {{ $value->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($value->blocked != null)
                                                        {{ $value->blocked }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($value->reason != null)
                                                        {{ $value->reason }}
                                                    @endif
                                                </td>
                                                <td>

                                                    <a href="#" class="btn btn-soft-success btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('unblock.destroy', $value->id)}}" title="{{ translate('Approve') }}">
                                                        <i class="las la-check"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="aiz-pagination">
                                {{-- {{ $blockUsers->appends(request()->input())->links() }} --}}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


<!-- delete Modal -->
<div id="delete-modal" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{translate('Un-Block Confirmation')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mt-1">{{translate('Are you sure to approve this?')}}</p>
                <a href="" id="delete-link" class="btn btn-primary mt-2">{{translate('Okay')}}</a>
                <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{translate('Cancel')}}</button>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
@endsection
