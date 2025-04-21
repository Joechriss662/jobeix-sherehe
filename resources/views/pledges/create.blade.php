@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Create New Pledge</h5>
                </div>

                <div class="card-body">
                    {{-- Display validation errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input:
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pledges.store') }}" id="pledgeForm">
                        @csrf

                        {{-- Event Selection --}}
<div class="mb-3">
    <label for="event_id" class="form-label">Event <span class="text-danger">*</span></label>
    <select class="form-select @error('event_id') is-invalid @enderror" 
            id="event_id" 
            name="event_id" 
            required>
        <option value="">-- Select Event --</option>
        @foreach($events as $event)
            <option value="{{ $event->id }}" 
                {{ old('event_id') == $event->id ? 'selected' : '' }}>
                {{ $event->name }} 
            </option>
        @endforeach
    </select>
    @error('event_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

                        {{-- Guest Selection --}}
                        <div class="mb-3">
                            <label for="guest_id" class="form-label">Guest <span class="text-danger">*</span></label>
                            <select class="form-select @error('guest_id') is-invalid @enderror" 
                                    id="guest_id" 
                                    name="guest_id" 
                                    required>
                                <option value="">-- Select Guest --</option>
                                @if(old('event_id'))
                                    @foreach(\App\Models\Event::find(old('event_id'))?->guests ?? [] as $guest)
                                        <option value="{{ $guest->id }}"
                                            {{ old('guest_id') == $guest->id ? 'selected' : '' }}>
                                            {{ $guest->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('guest_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Pledge Details --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="type" class="form-label">Pledge Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" 
                                        name="type" 
                                        required>
                                    <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="bank_transfer" {{ old('type') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Service</option>
                                    <option value="gift" {{ old('type') == 'gift' ? 'selected' : '' }}>Gift</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Tsh</span>
                                    <input type="number" 
                                           step="0.01" 
                                           min="0" 
                                           class="form-control @error('amount') is-invalid @enderror" 
                                           id="amount" 
                                           name="amount" 
                                           value="{{ old('amount') }}" 
                                           required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Additional Information --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deadline --}}
                        <div class="mb-3">
                            <label for="deadline" class="form-label">Deadline</label>
                            <input type="date" 
                                   class="form-control @error('deadline') is-invalid @enderror" 
                                   id="deadline" 
                                   name="deadline" 
                                   value="{{ old('deadline') }}"
                                   min="{{ date('Y-m-d') }}">
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Pledge Options --}}
                        <div class="mb-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_anonymous" 
                                       name="is_anonymous" 
                                       {{ old('is_anonymous') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_anonymous">
                                    Make this pledge anonymous
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_recurring" 
                                       name="is_recurring" 
                                       {{ old('is_recurring') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_recurring">
                                    This is a recurring pledge
                                </label>
                            </div>

                            <div class="row" id="recurrenceFields" style="{{ old('is_recurring') ? '' : 'display: none;' }}">
                                <div class="col-md-6">
                                    <label for="recurrence_frequency" class="form-label">Frequency</label>
                                    <select class="form-select" 
                                            id="recurrence_frequency" 
                                            name="recurrence_frequency">
                                        <option value="weekly" {{ old('recurrence_frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ old('recurrence_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="recurrence_ends" class="form-label">Ends After</label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="recurrence_ends" 
                                           name="recurrence_ends" 
                                           value="{{ old('recurrence_ends') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('pledges.index') }}" class="btn btn-secondary me-md-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Create Pledge
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle recurrence fields
    const isRecurring = document.getElementById('is_recurring');
    const recurrenceFields = document.getElementById('recurrenceFields');
    
    if (isRecurring) {
        isRecurring.addEventListener('change', function() {
            recurrenceFields.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Dynamic guest loading
    const eventSelect = document.getElementById('event_id');
    const guestSelect = document.getElementById('guest_id');
    
    if (eventSelect) {
        eventSelect.addEventListener('change', function() {
            const eventId = this.value;
            
            if (!eventId) {
                guestSelect.innerHTML = '<option value="">-- Select Guest --</option>';
                guestSelect.disabled = true;
                return;
            }
            
            fetch(`/events/${eventId}/guests`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    guestSelect.innerHTML = '<option value="">-- Select Guest --</option>';
                    data.guests.forEach(guest => {
                        const option = document.createElement('option');
                        option.value = guest.id;
                        option.textContent = guest.name;
                        guestSelect.appendChild(option);
                    });
                    guestSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error loading guests:', error);
                    guestSelect.innerHTML = '<option value="">Error loading guests</option>';
                });
        });
        
        // Trigger change if returning with validation errors
        if (eventSelect.value && eventSelect.value !== '') {
            eventSelect.dispatchEvent(new Event('change'));
        }
    }
});
</script>
@endsection
@endsection