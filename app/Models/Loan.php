<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Loan extends Model
{
    protected $fillable = ['user_id','book_id','loan_date','due_date','returned_at','status'];
    protected $casts = ['loan_date'=>'date','due_date'=>'date','returned_at'=>'date'];
    public function user() { return $this->belongsTo(User::class); }
    public function book() { return $this->belongsTo(Book::class); }
    public function getSituationAttribute(): string
    {
        if ($this->returned_at || $this->status === 'returned') return 'Devolvido';
        return $this->due_date->lt(Carbon::today()) ? 'Atrasado' : 'Emprestado';
    }
}
