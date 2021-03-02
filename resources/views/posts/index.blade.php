@extends('posts.layouts.app')

@section('title','Listagem')

@section('content')
    <a href="{{ route('posts.create') }}">Criar Novo</a>

    @if(session('message'))
        <div>{{ session('message') }}</div>
    @endif

    <form action="{{ route('posts.search') }}" method="post">
        @csrf
        <input type="text" name="search" id="search" value="{{ $filters['search'] }}">
        <button type="submit">Filtrar</button>
    </form>

    <h1>Posts</h1>

    @foreach($posts as $post)
        <p>
            <img src="{{ url("storage/{$post->image}") }}" alt="{{ $post->title }}" style="max-width:100px;">
            {{ $post->title }}[ <a href="{{ route('posts.show',['id'=>$post->id]) }}">Ver</a> | 
            <a href="{{ route('posts.edit',['id'=>$post->id]) }}">Editar</a> ]
        </p>
    @endforeach
    <hr>
    @if($filters)
        {{ $posts->appends($filters)->links() }}
    @else
        {{ $posts->links() }}
    @endif
@endsection

