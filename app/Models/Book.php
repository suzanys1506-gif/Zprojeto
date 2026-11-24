<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = ['title','author','publisher','category','publication_year','isbn','copies','available_copies'];
    public function loans() { return $this->hasMany(Loan::class); }
}
