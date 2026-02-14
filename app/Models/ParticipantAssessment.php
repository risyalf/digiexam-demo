<?php

namespace App\Models;

use App\Enum\ParticipantStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipantAssessment extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        "status" => ParticipantStatus::class,
        "last_status" => ParticipantStatus::class,
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }
}
