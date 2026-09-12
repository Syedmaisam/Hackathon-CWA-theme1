<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'issue_type', 'primary_authority_id', 'co_authority_ids',
    'internal_street_goes_to_tmc', 'flags', 'rule_note',
])]
class RoutingRule extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'issue_type';

    protected function casts(): array
    {
        return [
            'co_authority_ids' => 'array',
            'internal_street_goes_to_tmc' => 'boolean',
            'flags' => 'array',
        ];
    }

    public function primaryAuthority(): BelongsTo
    {
        return $this->belongsTo(Authority::class, 'primary_authority_id');
    }
}
