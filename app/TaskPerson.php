<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskPerson extends Model
{
    public $timestamps = false;
    protected $table = 'task_person';
    protected $fillable = ['personId', 'taskId'];
}
