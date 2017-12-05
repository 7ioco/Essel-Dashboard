<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public $fillable = ['id','project_no','address','client_name','client_address','title','start_date','status_msg','total_expenses_in_cents','price_in_cents','new_or_repeat_client'];
}
