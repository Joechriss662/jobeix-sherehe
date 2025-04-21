@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Create Invitation</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('invitations.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="event_id" class="form-label fw-bold">Select Event</label>
                    <select name="event_id" id="event_id" class="form-control">
                        <option value="" disabled selected>Select an Event</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="guest_id" class="form-label fw-bold">Select Guest</label>
                    <select name="guest_id" id="guest_id" class="form-control" disabled>
                        <option value="" disabled selected>Select a Guest</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Create Invitation</button>
                <a href="{{ route('invitations.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const eventSelect = document.getElementById('event_id');
        const guestSelect = document.getElementById('guest_id');

        eventSelect.addEventListener('change', function () {
            const eventId = this.value;

            // Clear the guest dropdown
            guestSelect.innerHTML = '<option value="" disabled selected>Loading...</option>';
            guestSelect.disabled = true;

            // Fetch guests for the selected event
            fetch(`/events/${eventId}/guests`)
                .then(response => response.json())
                .then(data => {
                    guestSelect.innerHTML = '<option value="" disabled selected>Select a Guest</option>';
                    data.forEach(guest => {
                        const option = document.createElement('option');
                        option.value = guest.id;
                        option.textContent = guest.name;
                        guestSelect.appendChild(option);
                    });
                    guestSelect.disabled = false;
                })
                .catch(error => console.error('Error fetching guests:', error));
        });
    });
</script>
@endsection
