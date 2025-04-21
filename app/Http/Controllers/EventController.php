<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    // Display a listing of the events.
    public function index(Request $request)
    {
        $query = Event::query();

        // Apply search filters
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
        }

        // Apply status filters
        if ($request->has('status') && $request->status === 'active') {
            $query->active();
        }

        // Paginate the results
        $events = $query->paginate(10);
        return view('events.index', compact('events'));
    }

    // Show the form for creating a new event.
    public function create()
    {
        return view('events.create');
    }

    // Store a newly created event in storage.
    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'start_time' => 'required|date',
            'status' => 'nullable',
            'event_code' => 'nullable|unique:events,event_code',
        ]);

        // Add the organizer_id (currently logged-in user's ID) and generate a unique event code
        $eventData = $request->all();
        $eventData['organizer_id'] = auth()->id();  // Set the organizer_id to the currently logged-in user
        $eventData['event_code'] = strtoupper(Str::slug($request->name, '-')) . '-' . Str::random(4);  // Generate a random event code based on the event name

        // Create the event with the provided data
        Event::create($eventData);

        // Redirect back with a success message
        return redirect()->route('events.index')->with('success', 'Event created successfully');
    }

    // Show details of a specific event
    public function show(Event $event)
    {
        $statistics = $event->getStatistics(); // Get event statistics
        $rsvpStats = $event->rsvpStatistics(); // Get RSVP statistics

        return view('events.show', compact('event', 'statistics', 'rsvpStats'));
    }

    // Show the form to edit an event
    public function edit(Event $event)
    {
        // Ensure only the organizer can edit the event
        if ($event->organizer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('events.edit', compact('event'));
    }

    // Update event details
    public function update(Request $request, Event $event)
    {
        // Ensure only the organizer can update the event
        if ($event->organizer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'start_time' => 'required|date',
            'status' => 'nullable',
            'event_code' => 'nullable',
        ]);

        $event->update($request->all());

        return redirect()->route('events.index')->with('success', 'Event updated successfully!');
    }

    // Delete an event
    public function destroy(Event $event)
    {
        // Ensure only the organizer can delete the event
        if ($event->organizer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted');
    }

    // Get guests for a specific event
    public function getGuests(Event $event)
    {
        return response()->json($event->guests);
    }

    // Get active and upcoming events
    public function getActiveUpcomingEvents()
    {
        $activeEvents = Event::active()->upcoming()->get();
        return response()->json($activeEvents);
    }
}
