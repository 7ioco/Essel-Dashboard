<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Employee extends Model
{
    public $fillable = ['id','full_name','email_address','photo_path'];
}