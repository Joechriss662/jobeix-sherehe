<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Pledge extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'event_id',
        'guest_id',
        'type',
        'amount',
        'description',
        'status'=> 'pending',
        'deadline',
        'is_anonymous',
        'is_recurring',
        'recurrence_frequency',
        'admin_notes'
    ];

    protected $casts = [
        'deadline' => 'date',
        'is_anonymous' => 'boolean',
        'is_recurring' => 'boolean',
    ];

    protected $appends = [
        'remaining_balance',
        'completion_percentage'
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    // Accessors
    public function getRemainingBalanceAttribute()
    {
        return $this->amount - $this->contributions()->sum('amount');
    }

    public function getCompletionPercentageAttribute()
    {
        if ($this->amount == 0) return 100;
        return round(($this->contributions()->sum('amount') / $this->amount) * 100, 2);
    }

    // Method for updating the status of pledge
    public function updateStatus()
{
    $paidAmount = $this->contributions()->sum('amount');
    $currentDate = now();

    \Log::info('Updating status for pledge ID: ' . $this->id, [
        'paidAmount' => $paidAmount,
        'pledgeAmount' => $this->amount,
        'currentDate' => $currentDate,
        'deadline' => $this->deadline,
    ]);

    if ($paidAmount >= $this->amount) {
        $this->update(['status' => 'fulfilled']);
    } elseif ($paidAmount > 0) {
        if ($currentDate->greaterThan($this->deadline)) {
            $this->update(['status' => 'overdue']);
        } else {
            $this->update(['status' => 'partially_fulfilled']);
        }
    } else {
        if ($currentDate->greaterThan($this->deadline)) {
            $this->update(['status' => 'overdue']);
        } else {
            $this->update(['status' => 'pending']);
        }
    }
}

    // Events
    protected static function booted()
    {
        static::creating(function ($pledge) {
            if (empty($pledge->deadline)) {
                $pledge->deadline = now()->addWeeks(2);
            }
        });
    }
}