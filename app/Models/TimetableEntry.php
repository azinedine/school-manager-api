<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableEntry extends Model
{
    protected $fillable = [
        'user_id',
        'day',
        'time_slot',
        'class',
        'mode',
        'group',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
