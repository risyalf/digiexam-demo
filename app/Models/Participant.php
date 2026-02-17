<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Participant extends Authenticatable
{
    use HasUuids, HasApiTokens;

    protected $guarded = [];

    protected $hidden = [
        'test_password',
    ];

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
