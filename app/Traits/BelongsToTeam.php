<?php
// app/Traits/BelongsToTeam.php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTeam
{
    protected static function bootBelongsToTeam()
    {
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->currentTeam) {
                $model->team_id = auth()->user()->current_team_id;
            }
        });
        
        static::addGlobalScope('team', function (Builder $builder) {
            if (auth()->check() && auth()->user()->currentTeam) {
                $builder->where($builder->getQuery()->from . '.team_id', auth()->user()->current_team_id);
            }
        });
    }
    
    public function team()
    {
        return $this->belongsTo(\App\Models\Team::class);
    }
    
    public function scopeForTeam(Builder $query, $teamId)
    {
        return $query->withoutGlobalScope('team')->where('team_id', $teamId);
    }
    
    public function scopeAllTeams(Builder $query)
    {
        return $query->withoutGlobalScope('team');
    }
}
