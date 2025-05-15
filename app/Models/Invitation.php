<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Invitation extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = [
        'event_id', 
        'guest_id',
        'code', 
        'status', 
        'sent_at'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}