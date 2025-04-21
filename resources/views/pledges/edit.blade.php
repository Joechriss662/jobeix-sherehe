@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Pledge: {{ $pledge->id }}</h2>

     <!-- Display Validation Errors -->
     @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('pledges.update', $pledge) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Pledge Information</h5>
            </div>
            <div class="card-body">
                <!-- Event Field First -->
                <div class="form-group">
                    <label for="event_id">Event <span class="text-danger">*</span></label>
                    <select name="event_id" id="event_id" class="form-control @error('event_id') is-invalid @enderror" required>
                        <option value="">Select Event</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ $pledge->event_id == $event->id ? 'selected' : '' }}>
                                {{ $event->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('event_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Guest Field Follows -->
                <div class="form-group">
                    <label for="guest_id">Guest <span class="text-danger">*</span></label>
                    <select name="guest_id" id="guest_id" class="form-control @error('guest_id') is-invalid @enderror" required>
                        <option value="">Select Guest</option>
                        @foreach($guests as $guest)
                            <option value="{{ $guest->id }}" {{ $pledge->guest_id == $guest->id ? 'selected' : '' }}>
                                {{ $guest->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('guest_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="amount">Amount <span class="text-danger">*</span></label>
                    <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $pledge->amount) }}" required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="deadline">Deadline <span class="text-danger">*</span></label>
                    <input type="date" name="deadline" id="deadline" class="form-control @error('deadline') is-invalid @enderror" value="{{ old('deadline', $pledge->deadline->format('Y-m-d')) }}" required>
                    @error('deadline')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="type">Type <span class="text-danger">*</span></label>
                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="cash" {{ $pledge->type == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ $pledge->type == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="service" {{ $pledge->type == 'service' ? 'selected' : '' }}>Service</option>
                        <option value="gift" {{ $pledge->type == 'gift' ? 'selected' : '' }}>Gift</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $pledge->description) }}</textarea>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="is_anonymous" id="is_anonymous" class="form-check-input" {{ $pledge->is_anonymous ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_anonymous">Anonymous Pledge</label>
 </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="is_recurring" id="is_recurring" class="form-check-input" {{ $pledge->is_recurring ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_recurring">Recurring Pledge</label>
                </div>

                <div class="form-group" id="recurrence_frequency_group" style="{{ $pledge->is_recurring ? '' : 'display:none;' }}">
                    <label for="recurrence_frequency">Recurrence Frequency</label>
                    <select name="recurrence_frequency" id="recurrence_frequency" class="form-control @error('recurrence_frequency') is-invalid @enderror">
                        <option value="">Select Frequency</option>
                        <option value="weekly" {{ $pledge->recurrence_frequency == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $pledge->recurrence_frequency == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                    @error('recurrence_frequency')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="admin_notes">Admin Notes</label>
                    <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3">{{ old('admin_notes', $pledge->admin_notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Update Pledge</button>
            <a href="{{ route('pledges.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('event_id').addEventListener('change', function() {
        const eventId = this.value;

        fetch(`/get-guests?event_id=${eventId}`)
            .then(response => response.json())
            .then(data => {
                const guestSelect = document.getElementById('guest_id');
                guestSelect.innerHTML = '<option value="">Select Guest</option>'; // Clear previous options and add default

                data.forEach(guest => {
                    const option = document.createElement('option');
                    option.value = guest.id;
                    option.textContent = guest.name;
                    guestSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching guests:', error)); // Error handling for fetch
    });

    document.getElementById('is_recurring').addEventListener('change', function() {
        const recurrenceGroup = document.getElementById('recurrence_frequency_group');
        recurrenceGroup.style.display = this.checked ? '' : 'none';
    });
</script>
@endsection