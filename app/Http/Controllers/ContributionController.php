<?php

namespace App\Http\Controllers;

use App\Models\Pledge;
use App\Models\Contribution;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ContributionController extends Controller
{
    // Show contributions or event selection
    public function index(Request $request)
    {
        // Check if an event is selected
        if ($request->has('event')) {
            $event = Event::findOrFail($request->event); // Retrieve the selected event
            $contributions = Contribution::where('event_id', $event->id)->get(); // Filter contributions by event
            return view('contributions.index', compact('contributions', 'event'));
        }

        // If no event is selected, show the event selection dropdown
        $events = Event::all(); // Retrieve all events
        return view('contributions.index', compact('events'));
    }

    // Show the form for creating a new contribution for a pledge
    public function create(Pledge $pledge)
    {
        return view('contributions.create', compact('pledge'));
    }

    // Store a newly created contribution in storage
    public function store(Request $request, Pledge $pledge)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'method' => 'required|string',
            'transaction_reference' => 'nullable|string|max:255',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $contribution = new Contribution();
        $contribution->amount = $request->amount;
        $contribution->method = $request->method;
        $contribution->transaction_reference = $request->transaction_reference;
        $contribution->receipt_number = $this->generateReceiptNumber();
        $contribution->pledge_id = $pledge->id;

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
            $contribution->receipt_path = $path;
        }

        $contribution->save();

        // Update pledge status
        $pledge->updateStatus();

        return redirect()->route('pledges.show', $pledge)->with('success', 'Contribution added successfully!');
    }

    // Display a specific contribution (optional, can be removed if not needed)
    public function show(Pledge $pledge, Contribution $contribution)
    {
        return view('contributions.show', compact('pledge', 'contribution'));
    }

    // Show the form for editing a contribution
    public function edit(Pledge $pledge, Contribution $contribution)
    {
        return view('contributions.edit', compact('pledge', 'contribution'));
    }

    // Update a contribution
    public function update(Request $request, Pledge $pledge, Contribution $contribution)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'method' => 'required|string',
            'transaction_reference' => 'nullable|string|max:255',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $contribution->amount = $request->amount;
        $contribution->method = $request->method;
        $contribution->transaction_reference = $request->transaction_reference;

        if ($request->hasFile('receipt')) {
            // Delete the old receipt if it exists
            if ($contribution->receipt_path) {
                Storage::disk('public')->delete($contribution->receipt_path);
            }
            $path = $request->file('receipt')->store('receipts', 'public');
            $contribution->receipt_path = $path;
        }

        $contribution->save();

        // Update pledge status
        $pledge->updateStatus();

        return redirect()->route('pledges.show', $pledge)->with('success', 'Contribution updated successfully!');
    }

    // Delete a contribution
    public function destroy(Pledge $pledge, Contribution $contribution)
    {
        // Delete the receipt if it exists
        if ($contribution->receipt_path) {
            Storage::disk('public')->delete($contribution->receipt_path);
        }

        $contribution->delete();

        // Update pledge status
        $pledge->updateStatus();

        return redirect()->route('pledges.show', $pledge)->with('success', 'Contribution deleted successfully!');
    }

    // Generate a unique receipt number
    private function generateReceiptNumber(): string
    {
        $lastReceipt = Contribution::orderBy('id', 'desc')->first();
        $nextNumber = $lastReceipt ? (int) substr($lastReceipt->receipt_number, 4) + 1 : 1; // Assuming "REC" is 3 characters long
        return 'REC-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT); // Format as REC-000001
    }
}