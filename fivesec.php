Создание полной системы для управления статьями с функционалом валидации, редактирования и удаления требует более глубокой интеграции между моделями, контроллерами и представлениями. Давайте рассмотрим, как можно построить такую систему в Laravel, пошагово описывая архитектуру и основные компоненты.

1. Модель Article
Сначала создадим модель, которая будет отвечать за хранение данных статей в базе данных.

// app/Models/Article.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
2. Контроллер ArticleController
Теперь создадим контроллер, который будет обрабатывать запросы на создание, редактирование, обновление и удаление статей.

// app/Http/Controllers/ArticleController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all();
        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        return view('articles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $article = Article::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('articles.index')->with('success', 'Статья успешно добавлена!');
    }

    public function edit($id)
    {
        $article = Article::findOrFail($id);
        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $article = Article::findOrFail($id);
        $article->update($validated);

        return redirect()->route('articles.index')->with('success', 'Статья успешно обновлена!');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Статья успешно удалена!');
    }
}
3. Маршруты
Далее, нужно настроить маршруты для доступа к этим действиям. В файле routes/web.php добавим маршруты, соответствующие действиям контроллера.

use App\Http\Controllers\ArticleController;

Route::resource('articles', ArticleController::class)->middleware('auth');
Эта строка автоматически создаст все необходимые маршруты для работы с ресурсом статей.

4. Представления
Теперь создадим представления для отображения статей и форм для создания/редактирования.

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
@endsection
5. Запуск миграций
Не забудьте создать миграцию для таблицы articles. В терминале выполните следующую команду:

php artisan make:migration create_articles_table
Затем в созданном файле миграции определите структуру таблицы:

// database/migrations/xxxx_xx_xx_xxxxxx_create_articles_table.php

public function up()
{
    Schema::create('articles', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('content');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}
После этого выполните миграции:

php artisan migrate
6. Аутентификация
Не забудьте, что для управления статьями необходима аутентификация. Это может быть сделано с помощью Laravel Breeze или Laravel Jetstream для более комплексной системы пользователей.

Заключение
Таким образом, вы создаете мини-систему управления статьями, из которой можно добавлять, редактировать и удалять статьи с применением валидации ввода и подтверждением удаления. Эта структура может служить основой для более сложных функций, таких как теги, категории, комментарии и т.д. После реализации этой базовой структуры вы сможете расширять функциональность в зависимости от нужд проекта.
