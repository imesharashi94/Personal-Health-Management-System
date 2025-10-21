<?php

namespace App\Policies;

use App\Models\Symptom;
use App\Models\User;

class SymptomPolicy
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
    public function view(User $user, Symptom $symptom): bool
    {
        return $user->id === $symptom->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Symptom $symptom): bool
    {
        return $user->id === $symptom->user_id;
    }
}

