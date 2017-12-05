<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class ExpenseEntry extends Model
{
    public $fillable = ['id','project_id','user_id','date','notes','amount_in_cents','created_at','updated_at'];
}