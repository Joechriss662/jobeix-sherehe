<?php

namespace App\Http\Controllers;

use App\Models\Pledge;
use App\Models\Event;
use App\Models\Guest;
use Illuminate\Http\Request;

class PledgeController extends Controller
{
  
    public function index()
    {
        $pledges = Pledge::with(['event', 'guest', 'contributions'])
            ->latest()
            ->paginate(10);

        return view('pledges.index', compact('pledges'));
    }

    
    public function create()
    {
        $events = Event::all();
        $events = Event::with('guests')->get();
        $guests = Guest::all();
        
        return view('pledges.create', compact('events'));
    }

    /**
     * Store a newly created pledge.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'guest_id' => 'required|exists:guests,id',
            'type' => 'required|in:cash,bank_transfer,service,gift',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'deadline' => 'nullable|date|after_or_equal:today',
            'is_anonymous' => 'sometimes|boolean',
            'is_recurring' => 'sometimes|boolean',
            'recurrence_frequency' => 'nullable|required_if:is_recurring,true|in:weekly,monthly',
        ]);

        try {
            $pledge = Pledge::create(array_merge($validated, [
                'status' => 'pending' // Default status
            ]));

            return redirect()
                ->route('pledges.show', $pledge)
                ->with('success', 'Pledge created successfully!');
                
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Pledge creation failed: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create pledge. Please try again.');
        }
    }

    /**
     * Display the specified pledge.
     */
    public function show(Pledge $pledge)
    {
        $pledge->load(['event', 'guest', 'contributions']);
        
        return view('pledges.show', compact('pledge'));
    }

    /**
     * Show the form for editing the specified pledge.
     */
    public function edit(Pledge $pledge)
    {
        $events = Event::all();
        $guests = Guest::all();
        
        return view('pledges.edit', compact('pledge', 'events', 'guests'));
    }

    /**
     * Update the specified pledge.
     */
    public function update(Request $request, Pledge $pledge)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'guest_id' => 'required|exists:guests,id',
            'type' => 'required|in:cash,bank_transfer,service,gift',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'deadline' => 'nullable|date|after_or_equal:today',
            'is_anonymous' => 'boolean',
            'is_recurring' => 'boolean',
            'recurrence_frequency' => 'nullable|in:weekly,monthly',
            'admin_notes' => 'nullable|string',
        ]);
    
        try {
            // Calculate total contributions
            $totalContributions = $pledge->contributions()->sum('amount');
    
            // Update status based on contributions
            if ($totalContributions >= $pledge->amount) {
                $pledge->status = 'fulfilled';
            } elseif ($totalContributions > 0) {
                $pledge->status = 'partially_fulfilled';
            } else {
                $pledge->status = 'pending';
            }
    
            // Update the pledge with the validated data
            $pledge->update($validated);
    
            return redirect()
                ->route('pledges.show', $pledge)
                ->with('success', 'Pledge updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to update pledge: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to update pledge. Please try again.');
        }
    }
    
    /**
     * Remove the specified pledge.
     */
    public function destroy(Pledge $pledge)
{
    $pledge->delete();
    session()->flash('success', 'Pledge deleted successfully!');
    return redirect()->route('pledges.index');
}
}
