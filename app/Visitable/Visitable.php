<?php

namespace App\Visitable;

use App\Models\Visit;
use App\Visitable\Concerns\FiltersByPopularityTimeframe;
use Illuminate\Database\Eloquent\Builder;

trait Visitable
{
    use FiltersByPopularityTimeframe;
    
    public function visit()
    {
        return new PendingVisit($this);
    }

    public function visits()
    {
        return $this->morphMany(Visit::class, 'visitable');
    }

    public function scopeWithTotalVisitCount(Builder $query)
    {
        return $query->withCount('visits as visit_count_total');
    }
}