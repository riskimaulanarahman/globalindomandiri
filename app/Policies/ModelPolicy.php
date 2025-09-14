<?php

namespace App\Policies;

use App\Models\User;

class ModelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin','Ops','Finance','Sales','Viewer']);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin','Ops','Finance','Sales']);
    }

    public function update(User $user): bool
    {
        return $user->hasAnyRole(['Admin','Ops','Finance','Sales']);
    }

    public function delete(User $user): bool
    {
        return $user->hasAnyRole(['Admin']);
    }
}

