<?php

namespace App\Models;

use App\Traits\HasAudit;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasAudit, HasUuids, SoftDeletes;

    protected $guarded = [];

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }
}
