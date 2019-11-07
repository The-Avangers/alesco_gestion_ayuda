<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public $timestamps = false;
    protected $table = 'project';
    protected $fillable = ['name', 'startDate', 'endDate', 'price', 'paid'];

}
