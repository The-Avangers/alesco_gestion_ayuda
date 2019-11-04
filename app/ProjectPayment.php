<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectPayment extends Model
{
    protected $table = 'project_payment';
    protected $fillable = ['projectId', 'amount', 'paymentDate'];
}
