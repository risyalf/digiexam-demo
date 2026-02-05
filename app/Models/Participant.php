<?php

namespace App\Models;

use App\Enum\ParticipantStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        "status" => ParticipantStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Participant $participant) {
            if (empty($participant->status)) {
                $participant->status = ParticipantStatus::IDLE;
            }
            if (empty($participant->last_status)) {
                $participant->last_status = ParticipantStatus::IDLE;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function participantGroup(): BelongsTo
    {
        return $this->belongsTo(ParticipantGroup::class);
    }
}
