<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectPayment extends Model
{
    protected $table = 'project_payment';
    public $timestamps = false;
    protected $fillable = ['projectId', 'amount', 'paymentDate'];
}
