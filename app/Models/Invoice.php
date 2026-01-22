<?php

namespace App\Models;

use App\Models\Invoice\InvoiceCode;
use Illuminate\Support\Facades\DB;
use App\Traits\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lib\Tenancy\Tenantable;

class Invoice extends Model
{
    use HasFactory,
        SoftDeletes,
        BelongsToTeam;

    protected $fillable = [
        'code',
        'customer_id',
        'status_id',
        'amount',
        'issue_date',
        'due_date',
        'payment_date',
        'team_id',
        'metadata',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'metadata' => 'array',
        'amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($invoice) {
            $invoice->code = InvoiceCode::generate($invoice->customer_id);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)
            ->withoutGlobalScope('team');
    }

    public function bankBillet()
    {
        return $this->hasOne(BankBillet::class);
    }
    
    public function isOverdue(): bool
    {
        return $this->due_date < now()
            && !$this->payment_date
            && in_array($this->status, ['issued']);
    }
    
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNull('payment_date')
            ->whereIn('status', ['issued']);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'invoice_product', 'invoice_id', 'product_id')
            ->withPivot('name', 'description', 'quantity', 'unit_price', 'amount') // Campos extras do pivot
            ->withTimestamps()
            ->wherePivot('deleted_at', null);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function delete()
    {
        DB::table('invoice_product')
            ->where('invoice_id', $this->id)
            ->whereNull('deleted_at')
            ->update(['deleted_at' => now()]);
            
        return parent::delete();
    }
    
    public function restore()
    {
        DB::table('invoice_product')
            ->where('invoice_id', $this->id)
            ->whereNotNull('deleted_at')
            ->update(['deleted_at' => null]);
            
        return parent::restore();
    }
}
