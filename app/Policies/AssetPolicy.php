<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageAssets();
    }

    public function view(User $user, Asset $asset): bool
    {
        return $user->canManageAssets();
    }

    public function create(User $user): bool
    {
        return $user->canManageAssets();
    }

    public function update(User $user, Asset $asset): bool
    {
        return $user->canManageAssets();
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $user->canManageAssets();
    }

    public function restore(User $user, Asset $asset): bool
    {
        return $user->canManageAssets();
    }

    public function deleteAny(User $user): bool
    {
        return $user->canManageAssets();
    }

    public function restoreAny(User $user): bool
    {
        return $user->canManageAssets();
    }

    public function forceDelete(User $user, Asset $asset): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
