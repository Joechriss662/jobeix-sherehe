<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    // Store a new guest (single input or bulk data)
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'bulk_data' => 'nullable|string',
        ]);

        if ($request->filled('bulk_data')) {
            $bulkData = explode("\n", $request->input('bulk_data'));
            $guests = [];

            foreach ($bulkData as $line) {
                $fields = array_map('trim', explode(',', $line));
                if (count($fields) >= 1) {
                    $guests[] = [
                        'name' => $fields[0] ?? null,
                        'phone' => $fields[1] ?? null,
                        'email' => $fields[2] ?? null,
                        'event_id' => $event->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($guests)) {
                Guest::insert($guests);
            }

            return redirect()->route('events.show', $event->id)->with('success', 'Guests added successfully!');
        }

        // Handle single guest input
        Guest::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'event_id' => $event->id,
        ]);

        return redirect()->route('events.show', $event->id)->with('success', 'Guest added successfully!');
    }

    // Update guest details
    public function update(Request $request, Guest $guest)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
        ]);

        $guest->update($request->only(['name', 'phone', 'email']));

        return redirect()->route('events.show', $guest->event_id)
                         ->with('success', 'Guest details updated successfully!');
    }

    // Delete a guest
    public function destroy(Guest $guest)
    {
        $guest->delete();

        return redirect()->route('events.show', $guest->event_id)
                         ->with('success', 'Guest deleted successfully!');
    }

    // Bulk import guests (CSV, copy-paste, or manual input)
    public function bulkImport(Request $request, Event $event)
    {
        $request->validate([
            'guests.*.name' => 'required|string|max:255',
            'guests.*.phone' => 'nullable|string|max:15',
            'guests.*.email' => 'nullable|email|max:255',
        ]);

        $guests = [];
        foreach ($request->input('guests') as $guest) {
            $guests[] = [
                'name' => $guest['name'],
                'phone' => $guest['phone'] ?? null,
                'email' => $guest['email'] ?? null,
                'event_id' => $event->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($guests)) {
            Guest::insert($guests);
        }

        return redirect()->route('events.show', $event->id)
                         ->with('success', 'Guests imported successfully!');
    }

    public function index(Request $request)
    {
        $events = Event::all(); // Fetch all events
        $selectedEvent = null;

        if ($request->has('event_id')) {
            $selectedEvent = Event::find($request->input('event_id'));
        } elseif ($events->count() === 1) {
            $selectedEvent = $events->first(); // Automatically select the only event
        }

        $guests = $selectedEvent ? $selectedEvent->guests : collect(); // Fetch guests for the selected event

        return view('guests.index', compact('events', 'selectedEvent', 'guests'));
    }
}
