<?php

namespace App\Models;

use App\Traits\HasAudit;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGroup extends Model
{
    use HasAudit, HasUuids, SoftDeletes;

    protected $fillable = ['name', 'created_by', 'updated_by', 'deleted_by'];

    protected $guarded = [];
}
