<?php

namespace App\Models;

use App\Enum\AssessmentStatus;
use App\Enum\AssessmentType;
use App\Traits\HasAudit;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assessment extends Model
{
    use HasAudit, HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'type' => AssessmentType::class,
            'status' => AssessmentStatus::class
        ];
    }
}
