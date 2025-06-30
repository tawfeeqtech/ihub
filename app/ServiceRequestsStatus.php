<?php

namespace App;

enum ServiceRequestsStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Rejected = 'rejected';

    public function label(): string
    {
        return __('filament.Widgets.AdminServiceRequestsByStatusChart.labels.' . $this->value);
    }
}
