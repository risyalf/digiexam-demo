<?php

namespace App\Models;

use App\Enum\ParticipantStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParticipantAssessment extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        "status" => ParticipantStatus::class,
        "last_status" => ParticipantStatus::class,
    ];

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'id', 'assessment_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class, 'id', 'participant_id');
    }
}
