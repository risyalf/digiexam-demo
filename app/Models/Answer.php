<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function participantAssessment(): BelongsTo
    {
        return $this->belongsTo(ParticipantAssessment::class);
    }
}
