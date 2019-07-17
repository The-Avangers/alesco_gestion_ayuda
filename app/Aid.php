<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Aid extends Model
{
    protected $table = 'Aid';
        public $timestamps = false;
        protected $fillable = [
            'name', 'measure', 'type', 'unit'
        ];
}
