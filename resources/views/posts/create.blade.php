@extends('posts.layouts.app')

@section('title','Cadastro')

@section('content')
    <h1>Novo Post<h1>

    <form action="{{ route('posts.store') }}" method="post" enctype="multipart/form-data">
        @include('posts._partials.form')    
    </form>
@endsection

