<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Contribution extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'pledge_id',
        'amount',
        'method',
        'transaction_reference',
        'receipt_number',
        'receipt_path',
        'payment_date',
        'notes'
    ];

    protected $casts = [
        'payment_date' => 'date'
    ];

    // Relationships
    public function pledge()
    {
        return $this->belongsTo(Pledge::class);
    }

    // Events
    protected static function booted()
    {
        static::created(function ($contribution) {
            $contribution->pledge->updateStatus();
        });

        static::updated(function ($contribution) {
            $contribution->pledge->updateStatus();
        });

        static::deleted(function ($contribution) {
            $contribution->pledge->updateStatus();
        });
    }
}