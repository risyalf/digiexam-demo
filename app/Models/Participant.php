<?php

namespace App\Models;

use App\Enum\ParticipantStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Participant extends Model
{
    use HasUuids, HasApiTokens;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function participantGroup(): BelongsTo
    {
        return $this->belongsTo(ParticipantGroup::class);
    }

    public function participantAssessments(): HasMany
    {
        return $this->hasMany(ParticipantAssessment::class, 'participant_id', 'id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
