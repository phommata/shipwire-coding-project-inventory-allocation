<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

Class Inventory extends Model

{
    protected $fillable = ['name', 'quantity'];
}
