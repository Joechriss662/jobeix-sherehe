@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Success and Error Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @else
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert" style="display: none;">
            <!-- This will be dynamically updated by JavaScript -->
        </div>
    @endif

    <!-- Event Details Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Event Details</h3>
        </div>
        <div class="card-body">
            <h4 class="card-title">{{ $event->name }}</h4>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->start_time)->format('F j, Y g:i A') }}</p>
            <p><strong>Location:</strong> {{ $event->location }}</p>
            <p><strong>Capacity:</strong> {{ $event->capacity }}</p>
            <p><strong>Description:</strong> {{ $event->description ?? 'No description provided.' }}</p>
        </div>
    </div>

    <!-- Button to trigger the Add Guest form -->
    <button class="btn btn-success mb-3" id="addGuestBtn">
        <i class="bi bi-person-plus"></i> Add Guest
    </button>

    <!-- Bulk Send Invitations Button 
    <button class="btn btn-primary mb-3" id="bulkSendInvitationsBtn">
        <i class="bi bi-envelope-fill"></i> Send Invitations to All
    </button>-->

    <!-- Guest Add Form (hidden initially) -->
    <div class="card shadow-sm mb-4" id="addGuestForm" style="display: none;">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Add Guest to Event</h4>
        </div>
        <div class="card-body">
            @include('guests._form', ['guest' => null, 'event' => $event, 'bulk' => true])
        </div>
    </div>

    <!-- Guests Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0">Guests List</h4>
        </div>
        <div class="card-body">
            @if ($event->guests->isEmpty())
                <p class="text-muted">No guests have been added to this event yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="guestsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($event->guests as $guest)
                                <tr id="guest_{{ $guest->id }}">
                                    <td>{{ $guest->name }}</td>
                                    <td>{{ $guest->phone }}</td>
                                    <td class="text-center">
                                        @php
                                            $hasInvitation = $guest->invitations->where('event_id', $event->id)->isNotEmpty();
                                        @endphp

                                        <!-- Invite Button -->
                                        @if ($hasInvitation)
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="bi bi-check-circle"></i> Invited
                                            </button>
                                        @else
                                            <button class="btn btn-success btn-sm inviteBtn" 
                                                data-guest-id="{{ $guest->id }}" 
                                                data-event-id="{{ $event->id }}">
                                                <i class="bi bi-envelope"></i> Invite
                                            </button>
                                        @endif

                                        <!-- Edit Button -->
                                        <button class="btn btn-info btn-sm editBtn" 
                                            data-guest-id="{{ $guest->id }}" 
                                            data-guest-name="{{ $guest->name }}" 
                                            data-guest-phone="{{ $guest->phone }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>

                                        <!-- Delete Button -->
                                        <form action="{{ route('guests.destroy', $guest->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Guest Edit Form (hidden initially) -->
    <div class="card shadow-sm mb-4" id="editGuestForm" style="display: none;">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0">Edit Guest Details</h4>
        </div>
        <div class="card-body">
            <form id="guestFormEdit" action="{{ route('guests.update', ['guest' => ':id']) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="editGuestName" class="form-label">Guest Name</label>
                    <input type="text" name="name" id="editGuestName" class="form-control" placeholder="Enter guest name">
                </div>

                <div class="mb-3">
                    <label for="editGuestPhone" class="form-label">Phone</label>
                    <input type="text" name="phone" id="editGuestPhone" class="form-control" placeholder="Enter phone number">
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Save
                    </button>
                    <button type="button" class="btn btn-secondary ms-2" id="cancelEditBtn">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Show the guest add form when the "Add Guest" button is clicked
        document.getElementById('addGuestBtn').addEventListener('click', function() {
            document.getElementById('addGuestForm').style.display = 'block';
            document.getElementById('editGuestForm').style.display = 'none';
        });

        // Show the edit guest form when the "Edit" button is clicked
        document.querySelectorAll('.editBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                let guestId = this.getAttribute('data-guest-id');
                let guestName = this.getAttribute('data-guest-name');
                let guestPhone = this.getAttribute('data-guest-phone');
                
                // Pre-fill the form with current guest details
                document.getElementById('editGuestName').value = guestName;
                document.getElementById('editGuestPhone').value = guestPhone;

                // Change the form's action to update the guest (using PUT)
                let form = document.getElementById('guestFormEdit');
                form.action = `{{ route('guests.update', ':id') }}`.replace(':id', guestId);

                // Show the edit form
                document.getElementById('addGuestForm').style.display = 'none';
                document.getElementById('editGuestForm').style.display = 'block';
            });
        });

        // Hide the edit guest form when "Cancel" is clicked
        document.getElementById('cancelEditBtn').addEventListener('click', function() {
            document.getElementById('editGuestForm').style.display = 'none';
        });

        // Handle "Invite" button click
        document.querySelectorAll('.inviteBtn').forEach(function (button) {
            button.addEventListener('click', function () {
                const guestId = this.getAttribute('data-guest-id');
                const eventId = this.getAttribute('data-event-id');
                const inviteButton = this;

                // Show loading state
                inviteButton.disabled = true;
                inviteButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Inviting...';

                // Send AJAX request to create an invitation
                fetch(`{{ route('invitations.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ guest_id: guestId, event_id: eventId }),
                })
                    .then(response => {
                        if (!response.ok) {
                            // If the response is not OK, throw an error with the response JSON
                            return response.json().then(err => {
                                throw new Error(err.error || 'An unknown error occurred.');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Redirect to invitations.index with success message
                            window.location.href = "{{ route('invitations.index') }}";
                        }
                    })
                    .catch(error => {
                        // Display error message on the event's show page
                        const errorAlert = document.getElementById('errorAlert');
                        if (errorAlert) {
                            errorAlert.textContent = error.message;
                            errorAlert.style.display = 'block';
                        }
                    })
                    .finally(() => {
                        // Reset button state if not redirected
                        inviteButton.disabled = false;
                        inviteButton.innerHTML = '<i class="bi bi-envelope"></i> Invite';
                    });
        });
    });
});
</script>
@endsection
