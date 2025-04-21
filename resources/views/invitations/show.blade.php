@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-info text-white">
            <h4>Invitation Details</h4>
        </div>
        <div class="card-body">
            <h2 class="fw-bold">{{ $invitation->guest_name }}</h2>
            <p><strong>Phone:</strong> {{ $invitation->guest_phone }}</p>
            <p><strong>Event:</strong> {{ $invitation->event->name ?? 'No Event' }}</p>
            <p><strong>RSVP Status:</strong> {{ $invitation->rsvp_status }}</p>

            <div class="d-flex justify-content-between">
                <a href="{{ route('invitations.edit', $invitation->id) }}" class="btn btn-warning">Edit Invitation</a>
                <a href="{{ route('invitations.index') }}" class="btn btn-secondary">Back to Invitations</a>
            </div>
        </div>
    </div>
</div>
@endsection
