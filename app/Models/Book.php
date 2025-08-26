<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Book extends Model
{
    use HasApiTokens, HasFactory,Notifiable;

    protected $fillable=['title','author','isbn','total_copies','available_copies'];

    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }
}
