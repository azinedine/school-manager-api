<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'wilaya_id',
        'municipality_id',
        'name',
        'name_ar',
        'address',
        'phone',
        'email',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the wilaya this institution belongs to.
     */
    public function wilaya(): BelongsTo
    {
        return $this->belongsTo(Wilaya::class);
    }

    /**
     * Get the municipality this institution belongs to.
     */
    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Get users at this institution.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope for active institutions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for filtering by wilaya.
     */
    public function scopeInWilaya($query, int $wilayaId)
    {
        return $query->where('wilaya_id', $wilayaId);
    }

    /**
     * Scope for filtering by municipality.
     */
    public function scopeInMunicipality($query, int $municipalityId)
    {
        return $query->where('municipality_id', $municipalityId);
    }

    /**
     * Scope for filtering by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
