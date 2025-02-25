<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    // Указываем таблицу, если название отличается от множественного числа класса
    protected $table = 'Articles';

    // Указываем, какие атрибуты можно массово присваивать
    protected $fillable = [
        'title',
        'content',
        'user_id',
        'created_at',
        'updated_at',    ];

    // Указываем, какие атрибуты должны быть скрыты в массиве и JSON представлении
    protected $hidden = [
       
    ];

    // Определяем отношения с другими моделями
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
