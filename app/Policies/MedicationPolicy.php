<?php

namespace App\Policies;

use App\Models\Medication;
use App\Models\User;

class MedicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Medication $medication): bool
    {
        return $user->id === $medication->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Medication $medication): bool
    {
        return $user->id === $medication->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Medication $medication): bool
    {
        return $user->id === $medication->user_id;
    }
}

