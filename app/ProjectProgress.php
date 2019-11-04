<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectProgress extends Model
{
    protected $table = 'project_progress';
    protected $fillable = ['projectId', 'milestone'];
}
