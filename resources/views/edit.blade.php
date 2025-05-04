@extends('layouts.layout')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error )
                <li>{{ $error}}</li>
            @endforeach
        </ul>

    </div>
@endif
<form action="{{route('update', $post)}}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="title">Title</label>
        <input type="text" name="title" value="{{$post->title}}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="content">Content</label>
        <textarea name="content" class="form-control" rows="5"   required> {{$post->content}}  </textarea>
    </div>
    <button type="submit" class="btn btn-primary">edit</button>
    <a href="{{route('index')}}" class="btn btn-secondary">Back</a>
</form>
@endsection