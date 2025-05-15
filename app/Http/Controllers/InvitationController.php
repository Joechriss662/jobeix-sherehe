<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Event;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InvitationController extends Controller
{
    // Display a list of invitations
    public function index(Request $request)
    {
        $invitations = Invitation::with(['guest', 'event'])->get();
        $events = Event::all();

        return view('invitations.index', compact('invitations', 'events'));
    }

    // Show form to create an invitation
    public function create()
    {
        $events = Event::with('guests')->get();
        return view('invitations.create', compact('events'));
    }

    // Store a new invitation
    public function store(Request $request)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'event_id' => 'required|exists:events,id',
        ]);

        // Check if an invitation already exists for this guest and event
        $existingInvitation = Invitation::where('guest_id', $request->guest_id)
            ->where('event_id', $request->event_id)
            ->first();

        if ($existingInvitation) {
            return response()->json(['error' => 'Invitation already exists for this guest and event.'], 400);
        }

        $event = Event::find($request->event_id);
        if (!$event) {
            return response()->json(['error' => 'Event not found.'], 404);
        }

        try {
            // Generate a unique invitation code
            $generatedCode = null;
            $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $event->name), 0, 3));
            if (strlen($prefix) < 3) { 
                $prefix = str_pad($prefix, 3, 'X'); 
            }
            if (empty($prefix)) {
                $prefix = "EVT"; 
            }

            $allowedChars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; 
            $randomLength = 4;

            do {
                $randomPart = '';
                for ($i = 0; $i < $randomLength; $i++) {
                    $randomPart .= $allowedChars[random_int(0, strlen($allowedChars) - 1)];
                }
                $generatedCode = $prefix . $randomPart;
                $existingCode = Invitation::where('code', $generatedCode)->first();
            } while ($existingCode);

            // Create the invitation
            $invitation = Invitation::create([
                'guest_id' => $request->guest_id,
                'event_id' => $request->event_id,
                'code' => $generatedCode, // Store the generated code
                'status' => 'pending', // Default status
            ]);

            return response()->json(['success' => 'Invitation created successfully!', 'invitation' => $invitation]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the invitation. Please try again.'], 500);
        }
    }

    // Show invitation details
    public function show(Invitation $invitation)
    {
        return view('invitations.show', compact('invitation'));
    }

    // Show form to edit an invitation
    public function edit(Invitation $invitation)
    {
        $events = Event::all();
        $guests = Guest::all();
        return view('invitations.edit', compact('invitation', 'events', 'guests'));
    }

    // Update an invitation
    public function update(Request $request, Invitation $invitation)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'guest_id' => 'required|exists:guests,id',
            'status' => 'required|in:pending,accepted,declined',
        ]);

        $invitation->update($request->all());
        return redirect()->route('invitations.index')->with('success', 'Invitation updated successfully.');
    }

    // Delete an invitation
    public function destroy(Invitation $invitation)
    {
        $invitation->delete();
        return redirect()->route('invitations.index')->with('success', 'Invitation deleted successfully.');
    }

    // Preview SMS for an invitation
    public function previewSms($id)
    {
        $invitation = Invitation::with('guest', 'event')->findOrFail($id);

        $eventDate = \Carbon\Carbon::parse($invitation->event->start_time);

        // Professional and user-friendly SMS message
        $message = "Habari {$invitation->guest->name},\n\n" .
                   "Umealikwa rasmi kwenye tukio la: *{$invitation->event->name}*.\n" .
                   "Tarehe: *{$eventDate->format('D, M d, Y')}* \nSaa: *{$eventDate->format('h:i A')}*\n" .
                   "Mahali: *{$invitation->event->location}*.\n\n" .
                   "Namba yako ya Mwaliko: *{$invitation->code}*.\n" .
                   "Tafadhali itunze kwa ajili ya uthibitisho au kujibu mwaliko.\n\n";

        // Optional: Contribution details. Consider if this is always needed in the first SMS.
        // For some events, this might be better placed on an RSVP confirmation page or a follow-up.
        $message .= "Mchango wako ni muhimu katika kufanikisha tukio hili. Unaweza kuchangia kupitia:\n" .
                    "- M-PESA: 0769 249 xxx\n" .
                    "- TIGO PESA: 0718 003 xxx\n" .
                    "- CRDB: 0152742041xxx (Jina: Eliamani E. Mbwambo)\n" .
                    "Tafadhali changia kabla ya *25/07/2025*.\n\n";

        $message .= "Tunatarajia ushiriki wako!\n" .
                    "Kwa niaba ya: *" . (isset($invitation->event->organizer->name) ? $invitation->event->organizer->name : 'Waandalizi wa Tukio') . "*";

        return response()->json(['message' => $message]);
    }

    // Send SMS for an invitation
    public function sendSms(Request $request, $id)
    {
        $request->validate([
            'smsForwarderAddress' => 'required|url',
            'message' => 'required|string',
        ]);

        session(['smsForwarderAddress' => $request->smsForwarderAddress]);

        $invitation = Invitation::with('guest', 'event')->findOrFail($id);

        if (!$invitation->guest || !$invitation->event) {
            return redirect()->route('invitations.index')->with('error', 'Invalid invitation data.');
        }

        $phoneNumber = $invitation->guest->phone;
        $message = $request->message;

        \Log::info("Sending SMS to {$phoneNumber} with message: {$message}");

        try {
            $response = Http::post("{$request->smsForwarderAddress}/sms/send", [
                'data' => [
                    'sim_slot' => 1,
                    'phone_numbers' => $phoneNumber,
                    'msg_content' => $message,
                ],
                'timestamp' => now()->timestamp * 1000,
                'sign' => '',
            ]);

            if ($response->successful()) {
                return redirect()->route('invitations.index')->with('success', 'SMS sent successfully!');
            } else {
                \Log::error('SMSForwarder API Error:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return redirect()->route('invitations.index')->with('error', 'Failed to send SMS. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('SMSForwarder Connection Error:', [
                'message' => $e->getMessage(),
            ]);
            return redirect()->route('invitations.index')->with('error', 'Failed to connect to the SMS gateway.');
        }
    }

    // Perform bulk actions on invitations
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'invitations' => 'required|array',
        ]);

        $invitations = Invitation::whereIn('id', $request->invitations)->get();

        if ($request->action === 'send_sms') {
            foreach ($invitations as $invitation) {
                // Prepare the SMS message
                $message = "Hello {$invitation->guest->name}, you are invited to the event '{$invitation->event->name}' on {$invitation->event->start_time}. Please RSVP.";

                // Send SMS using the provided API
                Http::post('http://smsserver.dc.konzo.xyz/odata/odata/Texts', [
                    'target' => $invitation->guest->phone,
                    'message' => $message,
                    'rqStatusReport' => true,
                ]);
            }
            return redirect()->route('invitations.index')->with('success', 'SMS sent to selected invitations.');
        }

        if ($request->action === 'update_status') {
            foreach ($invitations as $invitation) {
                $invitation->update(['status' => 'accepted']); // Example: Update to "accepted"
            }
            return redirect()->route('invitations.index')->with('success', 'RSVP status updated for selected invitations.');
        }

        return redirect()->route('invitations.index')->with('error', 'Invalid action selected.');
    }

    // Function to send a test WhatsApp message
    public function sendTestWhatsAppMessage(Request $request)
    {
        $phoneNumberId = env('656895627498642'); // Your Phone Number ID
        $accessToken = env('EAA66g1UhAM4BO4DyybT0ZCOiRLadxav76qbKIlJWp7Nv4SbExHqs6b5puBKd5FQENBIuscYIDroLnZCACzsoZAiqT0MsYKvJ08HDFHpjs9QFNEONMS9PJljb2an2ZAhT5IYRbSnlP6qGFfDgBuSEIh2ZCOoeuqm4TMDcV4eHW3vgZA9FV3IvG7yWDmUCXrph7OvpasQXLg5380wT0ZB1VJlmLWu4VVkT9WDGlkZD'); // Your Temporary Access Token
        $testPhoneNumber = env('+15556450876'); // Your Test Phone Number

        $response = Http::withHeaders([
            'Authorization' => "Bearer $accessToken",
            'Content-Type' => 'application/json',
        ])->post("https://graph.facebook.com/v15.0/$phoneNumberId/messages", [
            'messaging_product' => 'whatsapp',
            'to' => $testPhoneNumber,
            'type' => 'text',
            'text' => [
                'body' => $message,
            ],
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            $status = 'success';
            $message = 'WhatsApp message sent successfully!';
        } else {
            $status = 'error';
            $message = 'Failed to send WhatsApp message. Please try again.';
        }

        // Return the response to a view
        return view('whatsapp.response', compact('status', 'message'));
    }
}
