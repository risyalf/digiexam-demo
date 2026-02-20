<?php

namespace App\Models;

use App\Enum\ParticipantStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->belongsTo(Assessment::class, 'assessment_id', 'id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id', 'id');
    }
    
    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class, 'id', 'participant_assessment_id');
    }
}
