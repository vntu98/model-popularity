<?php

namespace App\Visitable\Concerns;

use Carbon\Carbon;

trait SetsPendingIntervals
{
    public function hourlyIntervals()
    {
        $this->interval = now()->subHour();

        return $this;
    }

    public function dailyIntervals()
    {
        $this->interval = now()->subDay();

        return $this;
    }

    public function weeklyIntervals()
    {
        $this->interval = now()->subWeek();

        return $this;
    }

    public function monthlyIntervals()
    {
        $this->interval = now()->subMonth();

        return $this;
    }

    public function customIntervals(Carbon $interval)
    {
        $this->interval = $interval;

        return $this;
    }
}