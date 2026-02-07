<?php

namespace App\Models;

use App\Traits\HasAudit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestQuestionOption extends Model
{
    use HasAudit, SoftDeletes;

    protected $guarded = [];

    public function testQuestion(): BelongsTo
    {
        return $this->belongsTo(TestQuestion::class);
    }
}
