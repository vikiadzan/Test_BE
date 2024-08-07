<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    use HasFactory;
    protected $guarded = [];

    public $timestamps = false;

    public function member()
    {
        return $this->belongsTo(Member::class,'code_member', 'code');
    }

    public function book()
    {
        return $this->belongsTo(Book::class,'code_book', 'code');
    }
}
