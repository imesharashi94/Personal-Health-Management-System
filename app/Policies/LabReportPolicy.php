<?php

namespace App\Policies;

use App\Models\LabReport;
use App\Models\User;

class LabReportPolicy
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
    public function view(User $user, LabReport $labReport): bool
    {
        return $user->id === $labReport->user_id || $user->isAdmin();
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
    public function update(User $user, LabReport $labReport): bool
    {
        return $user->id === $labReport->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LabReport $labReport): bool
    {
        return $user->id === $labReport->user_id;
    }
}

