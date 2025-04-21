@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">Manage Invitations</h2>
    </div>

    <!-- Success and Error Messages -->
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

    <!-- Filters -->
    <form method="GET" action="{{ route('invitations.index') }}" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <select name="event_id" class="form-select">
                    <option value="">All Events</option>
                    @foreach ($events as $event)
                        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                            {{ $event->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="declined" {{ request('status') == 'declined' ? 'selected' : '' }}>Declined</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by Guest Name" value="{{ request('search') }}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Filter</button>
    </form>

    <!-- Invitation List -->
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Invitation List</h5>
        </div>
        <div class="card-body">
            @if($invitations->isEmpty())
                <div class="alert alert-warning text-center" role="alert">
                    No invitations found.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Guest Name</th>
                                <th>Phone Number</th>
                                <th>Event</th>
                                <th>RSVP Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invitations as $invitation)
                                <tr>
                                    <td>{{ $invitation->guest->name ?? 'No Guest' }}</td>
                                    <td>{{ $invitation->guest->phone ?? 'No Phone' }}</td>
                                    <td>{{ $invitation->event->name ?? 'No Event' }}</td>
                                    <td>
                                        <span class="badge 
                                            {{ $invitation->status == 'pending' ? 'bg-warning' : ($invitation->status == 'accepted' ? 'bg-success' : 'bg-danger') }}">
                                            {{ ucfirst($invitation->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#smsForwarderModal" 
                                            data-invitation-id="{{ $invitation->id }}">
                                            <i class="bi bi-envelope"></i> Send SMS
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- SMSForwarder Modal -->
<div class="modal fade" id="smsForwarderModal" tabindex="-1" aria-labelledby="smsForwarderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="smsForwarderModalLabel">Send Invitation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="smsForwarderForm" method="POST" action="">
                @csrf
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <input type="hidden" name="invitation_id" id="invitationId">
                <div class="modal-body">
                    <!-- SMSForwarder Address -->
                    <div class="mb-3">
                        <label for="smsForwarderAddress" class="form-label">SMSForwarder Address</label>
                        <input type="text" class="form-control" id="smsForwarderAddress" name="smsForwarderAddress" 
                               placeholder="http://192.168.1.194:5000" 
                               value="{{ session('smsForwarderAddress', '') }}" required>
                        <small id="addressStatus" class="text-muted"></small>
                    </div>

                    <!-- Message Preview -->
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="8" readonly></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="sendSmsButton" disabled>Send SMS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const smsModal = document.getElementById('smsForwarderModal');
        const smsForwarderAddressInput = document.getElementById('smsForwarderAddress');
        const addressStatus = document.getElementById('addressStatus');
        const sendSmsButton = document.getElementById('sendSmsButton');

        // Populate modal with invitation details
        smsModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const invitationId = button.getAttribute('data-invitation-id');
            const smsForwarderForm = document.getElementById('smsForwarderForm');
            const messageField = document.getElementById('message');
            const invitationIdField = document.getElementById('invitationId');

            // Set the form action dynamically
            smsForwarderForm.action = `/invitations/send-sms/${invitationId}`;
            invitationIdField.value = invitationId;

            // Fetch the SMS preview
            fetch(`/invitations/preview-sms/${invitationId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        messageField.value = data.message; // Populate the message field with the template
                    } else {
                        messageField.value = 'Failed to load SMS preview.';
                    }
                })
                .catch(error => {
                    console.error('Error fetching SMS preview:', error);
                    messageField.value = 'Failed to load SMS preview.';
                });

            // Automatically validate the stored SMSForwarder address
            const storedAddress = smsForwarderAddressInput.value.trim();
            if (storedAddress) {
                validateSmsForwarderAddress(storedAddress);
            }
        });

        // Validate SMSForwarder Address
        smsForwarderAddressInput.addEventListener('input', function () {
            const address = this.value.trim();
            validateSmsForwarderAddress(address);
        });

        function validateSmsForwarderAddress(address) {
            addressStatus.textContent = 'Checking address...';
            addressStatus.classList.remove('text-success', 'text-danger');
            sendSmsButton.disabled = true;

            if (!address.startsWith('http://') && !address.startsWith('https://')) {
                addressStatus.textContent = 'Please enter a valid URL.';
                addressStatus.classList.add('text-danger');
                return;
            }

            fetch(`/proxy/status?address=${encodeURIComponent(address)}`, { method: 'GET' })
                .then(response => {
                    if (response.ok) {
                        addressStatus.textContent = 'Address is online.';
                        addressStatus.classList.add('text-success');
                        sendSmsButton.disabled = false;

                        // Store the address in the session
                        fetch('/store-smsforwarder-address', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify({ smsForwarderAddress: address }),
                        })
                            .then(storeResponse => storeResponse.json())
                            .then(storeData => {
                                if (storeData.success) {
                                    console.log('SMSForwarder address stored successfully.');
                                } else {
                                    console.error('Failed to store SMSForwarder address.');
                                }
                            })
                            .catch(storeError => {
                                console.error('Error storing SMSForwarder address:', storeError);
                            });
                    } else {
                        throw new Error('Address is not reachable.');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    addressStatus.textContent = 'Address is not reachable. Please check the URL.';
                    addressStatus.classList.add('text-danger');
                });
        }
    });
</script>
@endsection
