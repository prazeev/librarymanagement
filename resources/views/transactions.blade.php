@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <table class="table table-striped" width="100%">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Books</th>
                            <th>Expected Closure</th>
                            <th>Created at</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ucwords($transaction->type)}}</td>
                            <td>
                                @foreach($transaction->books as $book)
                                    {!! '<a href="'.route('book.details',['id' => $book->id]).'">'.$book->title.'</a>, ' !!}
                                @endforeach
                            </td>
                            <td>{{$transaction->expected_closure}}</td>
                            <td>{{$transaction->created_at}}</td>
                        </tr>
                        @endforeach
                        @if(count($transactions) == 0)
                            <div class="alert alert-warning" role="alert">
                                No results..
                            </div>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{$transactions->links()}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
