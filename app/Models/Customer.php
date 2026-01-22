<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Lib\Tenancy\Tenantable;
use App\Traits\BelongsToTeam;

class Customer extends Model
{
    use HasFactory,
        Notifiable,
        BelongsToTeam;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'document',
        'team_id',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
