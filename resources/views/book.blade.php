@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <table class="table table-striped">
                <tr>
                    <td colspan="2">
                        <center>{!! QrCode::size(300)->generate(route('book.transaction',['id' => $book->id])) !!}</center>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Date added</b>
                    </td>
                    <td>
                        {{$book->publication_date}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Author</b>
                    </td>
                    <td>
                        {{$book->author}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Publication date</b>
                    </td>
                    <td>
                        {{$book->publication_date}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Store Location</b>
                    </td>
                    <td>
                        {{$book->location}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>ISBN</b>
                    </td>
                    <td>
                        {{$book->isbn}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Available in stock</b>
                    </td>
                    <td>
                        {{$book->in_stock}}/{{$book->quantity}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Media Type</b>
                    </td>
                    <td>
                        <ul class="list-group">
                        @foreach($book->medias as $media)
                            <li class="list-group-item">{{$media->title}}</li>
                        @endforeach
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Courses</b>
                    </td>
                    <td>
                        <ul class="list-group">
                        @foreach($book->courses as $course)
                            <li  class="list-group-item">{{$course->course}}</li>
                        @endforeach
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Tags</b>
                    </td>
                    <td>
                        @foreach(explode(",", $book->keywords) as $tag)
                            {{$tag}},
                        @endforeach
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-9">
                            {{ $book->title }}
                        </div>
                        <div class="col-md-3">
                            @if($book->in_stock > 0)
                                <a href="{{route('book.borrow',['id' => $book->id])}}">Borrow</a>
                            @endif
                            @if(($book->transactions()->where('type','=','borrow')->where('user_id','=', auth()->user()->id)->get()->count() - $book->transactions()->where('type','=','return')->where('user_id','=', auth()->user()->id)->get()->count()) > 0)
                                    <a href="{{route('book.return',['id' => $book->id])}}">Return</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <center><img src="{{$cover}}" alt="{{ $book->title }}" class="img img-responsive img-bordered"></center>
                    <hr>
                    <p>{{$book->description}}</p>
                    <hr>
                    <p>
                        <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                            {{__("Attachments")}}
                        </a>
                    </p>
                    <div class="collapse" id="collapseExample">
                        <div class="card card-body">
                            <ul class="list-group">
                                @foreach($book->attachments as $attachment)
                                    <li class="list-group-item">
                                        <a href="{{\Illuminate\Support\Facades\Storage::url($attachment->path)}}" target="_blank">{{$attachment->path}}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
