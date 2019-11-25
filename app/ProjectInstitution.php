<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectInstitution extends Model
{
    protected $table = 'project_institution';
    public $timestamps = false;
    protected $fillable = ['projectId', 'institutionId'];
    //
}
