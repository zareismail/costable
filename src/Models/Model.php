<?php

namespace Zareismail\Costable\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model as LaravelModel, SoftDeletes};   

class Model extends LaravelModel 
{ 
    use HasFactory, SoftDeletes;   
}
