<?php

namespace App\Policies;

use App\Models\Enrolment;
use App\Models\User;

class EnrolmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Enrolment $enrolment): bool
    {
        return $user->isAdmin() || $enrolment->student->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Enrolment $enrolment): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Enrolment $enrolment): bool
    {
        return $user->isAdmin();
    }
}
