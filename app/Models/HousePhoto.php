<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};

class HousePhoto extends Model
{
    use SoftDeletes;

    protected $fillable = ['house_id', 'photo'];
}
