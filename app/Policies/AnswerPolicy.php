<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Answer;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnswerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Answer');
    }

    public function view(AuthUser $authUser, Answer $answer): bool
    {
        return $authUser->can('View:Answer');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Answer');
    }

    public function update(AuthUser $authUser, Answer $answer): bool
    {
        return $authUser->can('Update:Answer');
    }

    public function delete(AuthUser $authUser, Answer $answer): bool
    {
        return $authUser->can('Delete:Answer');
    }

    public function restore(AuthUser $authUser, Answer $answer): bool
    {
        return $authUser->can('Restore:Answer');
    }

    public function forceDelete(AuthUser $authUser, Answer $answer): bool
    {
        return $authUser->can('ForceDelete:Answer');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Answer');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Answer');
    }

    public function replicate(AuthUser $authUser, Answer $answer): bool
    {
        return $authUser->can('Replicate:Answer');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Answer');
    }

}