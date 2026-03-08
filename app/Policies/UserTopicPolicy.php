<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UserTopic;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserTopicPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UserTopic');
    }

    public function view(AuthUser $authUser, UserTopic $userTopic): bool
    {
        return $authUser->can('View:UserTopic');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UserTopic');
    }

    public function update(AuthUser $authUser, UserTopic $userTopic): bool
    {
        return $authUser->can('Update:UserTopic');
    }

    public function delete(AuthUser $authUser, UserTopic $userTopic): bool
    {
        return $authUser->can('Delete:UserTopic');
    }

    public function restore(AuthUser $authUser, UserTopic $userTopic): bool
    {
        return $authUser->can('Restore:UserTopic');
    }

    public function forceDelete(AuthUser $authUser, UserTopic $userTopic): bool
    {
        return $authUser->can('ForceDelete:UserTopic');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UserTopic');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UserTopic');
    }

    public function replicate(AuthUser $authUser, UserTopic $userTopic): bool
    {
        return $authUser->can('Replicate:UserTopic');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UserTopic');
    }

}