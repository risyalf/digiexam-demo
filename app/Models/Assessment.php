<?php

namespace App\Models;

use App\Enum\AssessmentStatus;
use App\Enum\AssessmentType;
use App\Traits\HasAudit;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assessment extends Model
{
    use HasAudit, HasUuids, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'type' => AssessmentType::class,
        'status' => AssessmentStatus::class,
        'show_result' => 'boolean',
        'detail_result' => 'boolean',
        'need_token' => 'boolean',
        'randomize_question' => 'boolean',
    ];
    
    protected function castBoolean($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function setShowResultAttribute($value)
    {
        $this->attributes['show_result'] = (bool) $value;
    }

    public function setDetailResultAttribute($value)
    {
        $this->attributes['detail_result'] = (bool) $value;
    }

    public function setNeedTokenAttribute($value)
    {
        $this->attributes['need_token'] = (bool) $value;
    }

    public function setRandomizeQuestionAttribute($value)
    {
        $this->attributes['randomize_question'] = (bool) $value;
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function participant_groups(): BelongsToMany
{
    return $this->belongsToMany(
        ParticipantGroup::class, 
        'assessment_participant_groups',
        'assessment_id', 
        'participant_group_id'
    );
}
}
