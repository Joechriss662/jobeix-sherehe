<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'event_id'
    ];

    // Define the relationship with Pledge
    public function pledges()
    {
        return $this->hasMany(Pledge::class);
    }

    // Define the relationship with event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Define the relationship with Invitation
    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }
}
