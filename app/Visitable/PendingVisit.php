<?php

namespace App\Visitable;

use App\Models\User;
use App\Models\Visit;
use App\Visitable\Concerns\SetsPendingIntervals;
use Illuminate\Database\Eloquent\Model;

class PendingVisit
{
    use SetsPendingIntervals;

    protected $attributes = [];
    protected $interval;

    public function __construct(protected Model $model) 
    {
        $this->dailyIntervals();
    }

    public function withIp($ip = null)
    {
        $this->attributes['ip'] = $ip ?? request()->ip();

        return $this;
    }

    public function withUser(?User $user = null)
    {
        $this->attributes['user_id'] = $user?->id ?? auth()->user()->id;

        return $this;
    }

    public function withData($data)
    {
        $this->attributes = array_merge($this->attributes, $data);

        return $this;
    }

    protected function buildJsonColumns()
    {
        return collect($this->attributes)
            ->mapWithKeys(function ($value, $key) {
                return ['data->' . $key => $value];
            })->toArray();
    }

    protected function shouldBeLoggedAgain(Visit $visit)
    {
        return !$visit->wasRecentlyCreated && $visit->created_at->lt($this->interval);
    }

    public function __destruct()
    {
        $visit = $this->model->visits()->latest()->firstOrCreate($this->buildJsonColumns(), [
            'data' => $this->attributes
        ]);

        $visit->when($this->shouldBeLoggedAgain($visit), function () use ($visit) {
            $visit->replicate()->save();
        });
    }
}