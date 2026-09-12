<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'aliases', 'kind', 'parent_id', 'district',
    'tmc_authority_id', 'special_zone_authority_id', 'needs_human_review',
])]
class GazetteerNode extends Model
{
    protected function casts(): array
    {
        return [
            'aliases' => 'array',
            'needs_human_review' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function tmcAuthority(): BelongsTo
    {
        return $this->belongsTo(Authority::class, 'tmc_authority_id');
    }

    public function specialZoneAuthority(): BelongsTo
    {
        return $this->belongsTo(Authority::class, 'special_zone_authority_id');
    }
}
