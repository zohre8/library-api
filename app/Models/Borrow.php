<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    use HasFactory;
    protected $fillable=['user_id','book_id','due_at','returned_at'];
    protected $dates=['borrowed_at','due_at','returned_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function scopeActive($q)
    {
        return $q->whereNull('returned_at');
    }
}
