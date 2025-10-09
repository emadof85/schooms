<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attendance;
use App\Helpers\Qs;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can mark attendance for the given class.
     */
    public function mark(User $user, $class_id = null)
    {
        // Allow teamSA (admin, super_admin) and teachers
        return in_array($user->user_type, array_merge(Qs::getTeamSA(), ['teacher']));
    }
}
