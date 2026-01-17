<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lib\Tenancy\Tenantable;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [];
}
