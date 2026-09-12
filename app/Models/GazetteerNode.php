<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'aliases', 'kind', 'uc_code', 'parent_id', 'district',
    'tmc_authority_id', 'special_zone_authority_id', 'needs_human_review',
    'contact_name', 'contact_phone',
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

    /**
     * The town this node sits in, or itself if it already is one. Landmarks
     * nest up to three deep — Boat Basin sits under Clifton Block 2, which
     * sits under Clifton, which sits under Saddar — so this walks the chain
     * rather than reading `parent` once. The depth cap is a cycle guard.
     */
    public function town(): ?self
    {
        $node = $this;

        for ($hops = 0; $node && $node->kind !== 'town' && $hops < 5; $hops++) {
            $node = $node->parent;
        }

        return $node?->kind === 'town' ? $node : null;
    }

    public function specialZoneAuthority(): BelongsTo
    {
        return $this->belongsTo(Authority::class, 'special_zone_authority_id');
    }
}
