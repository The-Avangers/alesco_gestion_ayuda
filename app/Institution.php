<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $table = 'institution';
    public $timestamps = false;
    protected $fillable = ['name'];
}
