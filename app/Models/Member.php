<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function borrowedBooks()
    {
        return $this->hasMany(Borrow::class, 'code_member', 'code');
    }
}
