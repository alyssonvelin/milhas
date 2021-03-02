@extends('posts.layouts.app')

@section('title','Edição')

@section('content')
    <h1>Editando Post<h1>

    <form action="{{ route('posts.update',$post->id) }}" method="post" enctype="multipart/form-data">
        @method('PUT')
        @include('posts._partials.form')
    </form>
@endsection

