<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WeeklyReport;

class WeeklyReportPolicy
{
    public function viewAsCoordinator(User $user, WeeklyReport $report): bool
    {
        // Coordinator can view if they're assigned to this report
        return $user->isCoordinator() && $report->coordinator_user_id === $user->id;
    }

    public function updateStatus(User $user, WeeklyReport $report): bool
    {
        // Coordinator can update status if they're assigned to this report
        return $user->isCoordinator() && $report->coordinator_user_id === $user->id;
    }
}
