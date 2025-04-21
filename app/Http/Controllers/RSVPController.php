<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class RSVPController extends Controller
{
    public function respond(Request $request, Guest $guest)
    {
        $request->validate([
            'rsvp_status'=> 'required|in:accepted,declined',
        ]);

        $guest->update(['rsvp_status'=>$request->rsvp_status]);

        return redirect()->route('guests.index')->with('success','RSVP status updated successfully!');
    }
}
