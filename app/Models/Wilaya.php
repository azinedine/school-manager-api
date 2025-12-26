<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilaya extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'name_ar',
    ];

    /**
     * Get municipalities in this wilaya.
     */
    public function municipalities(): HasMany
    {
        return $this->hasMany(Municipality::class);
    }

    /**
     * Get institutions in this wilaya.
     */
    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class);
    }
}
