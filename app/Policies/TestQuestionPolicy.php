<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TestQuestion;
use Illuminate\Auth\Access\HandlesAuthorization;

class TestQuestionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TestQuestion');
    }

    public function view(AuthUser $authUser, TestQuestion $testQuestion): bool
    {
        return $authUser->can('View:TestQuestion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TestQuestion');
    }

    public function update(AuthUser $authUser, TestQuestion $testQuestion): bool
    {
        return $authUser->can('Update:TestQuestion');
    }

    public function delete(AuthUser $authUser, TestQuestion $testQuestion): bool
    {
        return $authUser->can('Delete:TestQuestion');
    }

    public function restore(AuthUser $authUser, TestQuestion $testQuestion): bool
    {
        return $authUser->can('Restore:TestQuestion');
    }

    public function forceDelete(AuthUser $authUser, TestQuestion $testQuestion): bool
    {
        return $authUser->can('ForceDelete:TestQuestion');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TestQuestion');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TestQuestion');
    }

    public function replicate(AuthUser $authUser, TestQuestion $testQuestion): bool
    {
        return $authUser->can('Replicate:TestQuestion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TestQuestion');
    }

}