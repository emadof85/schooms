<?php

namespace App\Policies;

use App\User;
use App\Models\Attendance;
use App\Helpers\Qs;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class AttendancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can mark attendance for the given class.
     */
    public function mark(User $user)
    {
        // Allow all staff
        return in_array($user->user_type, Qs::getStaff());
    }
}
