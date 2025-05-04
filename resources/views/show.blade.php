@extends('layouts.layout')

@section('content')
    <h1>{{$post->title}}</h1>
    <p>{{$post->content}}</p>
    <a href="{{route('index')}}" class="btn btn-secondary">Back</a>
@endsection