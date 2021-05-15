@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{translate("All Request
         " )}}</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Block Users')}}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{translate('Name')}}</th>
                    <th>{{translate('Blocker Name')}}</th>
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

@endsection
