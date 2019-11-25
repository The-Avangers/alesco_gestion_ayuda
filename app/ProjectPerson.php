<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectPerson extends Model
{
    protected $table = 'project_person';
    public $timestamps = false;
    protected $fillable = ['projectId', 'personId', 'role'];
}
