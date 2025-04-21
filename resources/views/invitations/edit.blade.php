@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-lg">
        <div class="card-header bg-warning text-white">
            <h4>Edit Invitation</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('invitations.update', $invitation->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="guest_name" class="form-label fw-bold">Guest Name</label>
                    <input type="text" name="guest_name" id="guest_name" class="form-control" value="{{ old('guest_name', $invitation->guest->name) }}" required placeholder="Enter guest name">
                </div>

                <div class="mb-3">
                    <label for="guest_phone" class="form-label fw-bold">Guest Phone</label>
                    <input type="tel" name="guest_phone" id="guest_phone" class="form-control" value="{{ old('guest_phone', $invitation->guest->phone) }}" required placeholder="Enter guest phone number">
                </div>

                <div class="mb-3">
                    <label for="event_id" class="form-label fw-bold">Select Event</label>
                    <select name="event_id" id="event_id" class="form-control">
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ $event->id == $invitation->event_id ? 'selected' : '' }}>
                                {{ $event->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="rsvp_status" class="form-label fw-bold">RSVP Status</label>
                    <select name="rsvp_status" id="rsvp_status" class="form-control">
                        <option value="pending" {{ $invitation->rsvp_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ $invitation->rsvp_status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="declined" {{ $invitation->rsvp_status == 'declined' ? 'selected' : '' }}>Declined</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Update Invitation</button>
                <a href="{{ route('invitations.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
