<?php

namespace App\Models;

use App\Traits\HasAudit;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestQuestion extends Model
{
    use HasAudit, HasUuids, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'show_once' => 'boolean',
    ];
}
