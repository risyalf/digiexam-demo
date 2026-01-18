<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentToken extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }
}
