<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Features;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\HasFeatures;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Cashier\Billable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Lib\Billing\ValidSubscription;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;
    use HasApiTokens;
    use HasProfilePhoto;
    use HasFeatures;
    use Impersonate;
    use SoftDeletes;
    use ValidSubscription;
    use HasTeams;
    use Billable;
    
    protected $fillable = [
        'name',
        'email',
        'role_id',
        'password',
    ];
    
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];
    
    protected $appends = [
        'profile_photo_url',
    ];
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::deleting(function (self $model) {
            $model->attributes['email'] = $model->attributes['email'].'.del-'.now()->unix();
            $model->saveQuietly();
            $model->ownedTeams()->each(function ($team) {
                $team->purge();
            });
        });
    }
    
    public function canImpersonate()
    {
        return Features::check(request(), 'admin');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->currentTeam) {
            return false;
        }

        return $this->hasTeamPermission($this->currentTeam, $permission);
    }
    
    public function currentTeamRole(): ?string
    {
        if (!$this->currentTeam) {
            return null;
        }

        if ($this->ownsTeam($this->currentTeam)) {
            return 'owner';
        }

        $role = $this->teamRole($this->currentTeam);
        
        return $role?->key;
    }
    
    public function currentTeamPermissions(): array
    {
        if (!$this->currentTeam) {
            return [];
        }

        $role = $this->currentTeamRole();

        if ($role === 'owner') {
            return ['*'];
        }

        return $this->teamPermissions($this->currentTeam);
    }
}
