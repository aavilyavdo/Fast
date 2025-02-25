<?
articles/index.blade.php
@extends('layouts.app')

@section('content')
    <h1>Список Статей</h1>
    <a href="{{ route('articles.create') }}" class="btn btn-primary">Добавить Статью</a>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <ul>
        @foreach($articles as $article)
            <li>
                <a href="{{ route('articles.edit', $article->id) }}">{{ $article->title }}</a>
                <form action="{{ route('articles.destroy', $article->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Вы уверены, что хотите удалить эту статью?');">Удалить</button>
                </form>
            </li>
        @endforeach
    </ul>
@endsection
articles/create.blade.php
@extends('layouts.app')

@section('content')
    <h1>Добавить Статью</h1>

    <form action="{{ route('articles.store') }}" method="POST">
        @csrf
        <div>
            <label for="title">Название:</label>
            <input type="text" name="title" required>
        </div>
        <div>
            <label for="content">Содержимое:</label>
            <textarea name="content" required></textarea>
        </div>
        <button type="submit">Сохранить</button>
    </form>
@endsection
articles/edit.blade.php
@extends('layouts.app')

@section('content')
    <h1>Редактировать Статью</h1>

    <form action="{{ route('articles.update', $article->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="title">Название:</label>
            <input type="text" name="title" value="{{ $article->title }}" required>
        </div>
        <div>
            <label for="content">Содержимое:</label>
            <textarea name="content" required>{{ $article->content }}</textarea>
        </div>
        <button type="submit">Обновить</button>
    </form>
@endsection?>
