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
                        <div class="card card-body">
                            <form method="get">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="course">{{__("Category")}}</label>
                                            <select name="course" class="form-control" id="course">
                                                <option value="">None</option>
                                                @foreach($courses as $id => $course)
                                                    <option value="{{$id}}" {{request()->course == $id ? "selected" : ''}}>{{$course}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="course">{{__("Media Type")}}</label>
                                            <select name="mediatype" class="form-control" id="mediatype">
                                                <option value="">None</option>
                                                @foreach($mediatype as $id => $media)
                                                    <option value="{{$id}}" {{request()->mediatype == $id ? "selected" : ''}}>{{$media}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="search"
                                           placeholder="Search Books, Author, ISBN or keywords"
                                           value="{{request()->search}}">
                                </div>
                                <button type="submit" class="btn btn-primary mb-2">Search</button>
                            </form>
                        </div>
                        <br>
                        <table class="table table-striped" width="100%">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>ISBN</th>
                                <th>Author</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($books as $book)
                                <tr class="{{$book->in_stock > 0 ? 'table-success' : 'table-danger'}}">
                                    <td>{!! '<a href="'.route('book.details',['id' => $book->id]).'">'.$book->title.'</a>, ' !!}</td>
                                    <td>{{$book->isbn}}</td>
                                    <td>{{$book->author}}</td>
                                    <td>{!! $book->in_stock > 0 ? $book->in_stock.'/'.$book->quantity.' Available' : 'Out of Stock' !!}</td>
                                </tr>
                            @endforeach
                            @if(count($books) == 0)
                                <div class="alert alert-warning" role="alert">
                                    No results..
                                </div>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{$books->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
