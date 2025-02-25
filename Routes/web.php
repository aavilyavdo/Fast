<?
use App\Http\Controllers\ArticleController;

Route::resource('articles', ArticleController::class)->middleware('auth');?
