<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait HasAudit
{
    public function initializeHasAudit()
    {
        $this->created_by = Auth::id() ?? User::first() ? User::first()->id : 1;
        $this->updated_by = Auth::id() ?? User::first() ? User::first()->id : 1;
    }

    public static function bootHasAudit()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id() ?? User::first() ? User::first()->id : 1;
            $model->updated_by = Auth::id() ?? User::first() ? User::first()->id : 1;
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id() ?? User::first() ? User::first()->id : 1;
        });

        static::deleting(function ($model) {
            if (Auth::check() && ! $model->isForceDeleting()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });

        // static::saving(function ($model) {
        //     $data = $model->getAttributes();
        //     $model->fill($data);
        // });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
