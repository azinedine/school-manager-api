<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipality extends Model
{
    use HasFactory;

    protected $fillable = [
        'wilaya_id',
        'name',
        'name_ar',
    ];

    /**
     * Get the wilaya this municipality belongs to.
     */
    public function wilaya(): BelongsTo
    {
        return $this->belongsTo(Wilaya::class);
    }

    /**
     * Get institutions in this municipality.
     */
    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class);
    }
}
