<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectInstitution extends Model
{
    protected $table = 'project_institution';
    protected $fillable = ['projectId', 'institutionId'];
    //
}
