<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ParticipantGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParticipantGroupPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ParticipantGroup');
    }

    public function view(AuthUser $authUser, ParticipantGroup $participantGroup): bool
    {
        return $authUser->can('View:ParticipantGroup');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ParticipantGroup');
    }

    public function update(AuthUser $authUser, ParticipantGroup $participantGroup): bool
    {
        return $authUser->can('Update:ParticipantGroup');
    }

    public function delete(AuthUser $authUser, ParticipantGroup $participantGroup): bool
    {
        return $authUser->can('Delete:ParticipantGroup');
    }

    public function restore(AuthUser $authUser, ParticipantGroup $participantGroup): bool
    {
        return $authUser->can('Restore:ParticipantGroup');
    }

    public function forceDelete(AuthUser $authUser, ParticipantGroup $participantGroup): bool
    {
        return $authUser->can('ForceDelete:ParticipantGroup');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ParticipantGroup');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ParticipantGroup');
    }

    public function replicate(AuthUser $authUser, ParticipantGroup $participantGroup): bool
    {
        return $authUser->can('Replicate:ParticipantGroup');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ParticipantGroup');
    }

}