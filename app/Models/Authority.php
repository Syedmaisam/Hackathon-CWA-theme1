<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id', 'name', 'short_name', 'kind', 'district', 'website', 'website_verified',
    'email', 'email_verified', 'phone', 'phone_verified', 'secondary_phone',
    'address', 'notes', 'citizen_visible',
])]
class Authority extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'website_verified' => 'boolean',
            'email_verified' => 'boolean',
            'phone_verified' => 'boolean',
            'citizen_visible' => 'boolean',
        ];
    }

    public function routingRulesAsPrimary(): HasMany
    {
        return $this->hasMany(RoutingRule::class, 'primary_authority_id');
    }
}
