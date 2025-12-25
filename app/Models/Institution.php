<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'wilaya_code',
        'municipality_id',
    ];
}
