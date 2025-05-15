<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Event extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = [
        'name',
        'description',
        'location',
        'start_time',
        'organizer_id',
        'capacity',
        'status',
        'event_code',
    ];

    // Relationship: An event has many invitations
    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    // Relationship: An event belongs to an organizer (user)
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    // Relationship: An event has many guests
    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    // Relationship: An event has many pledges
    public function pledges()
    {
        return $this->hasMany(Pledge::class);
    }

    // Relationship: An event has many contributions through pledges
    public function contributions()
    {
        return $this->hasManyThrough(Contribution::class, Pledge::class, 'event_id', 'pledge_id');
    }

    // Scope: Get active events
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope: Get events by organizer
    public function scopeByOrganizer($query, $organizerId)
    {
        return $query->where('organizer_id', $organizerId);
    }

    // Scope: Get upcoming events
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>=', now());
    }

    // Accessor: Format the start time
    public function getFormattedStartTimeAttribute()
    {
        return \Carbon\Carbon::parse($this->start_time)->format('F j, Y g:i A');
    }

    // Method: Get event statistics
    public function getStatistics()
    {
        return [
            'total_guests' => $this->guests()->count(),
            'total_invitations' => $this->invitations()->count(),
            'total_contributions' => $this->contributions()->sum('contributions.amount'),
        ];
    }

    // Method: Get RSVP statistics
    public function rsvpStatistics()
    {
        return [
            'accepted' => $this->invitations()->where('status', 'accepted')->count(),
            'declined' => $this->invitations()->where('status', 'declined')->count(),
            'pending' => $this->invitations()->where('status', 'pending')->count(),
        ];
    }
}
