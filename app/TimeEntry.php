<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class TimeEntry extends Model
{
    public $fillable = ['id','project_id','employee_id','date_performed','notes','time_in_minutes','rate_in_cents','created_at','updated_at'];
}