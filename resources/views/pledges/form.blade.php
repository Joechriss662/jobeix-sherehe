<div class="card">
    <div class="card-header">
        <h5>{{ $title }}</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ $action }}">
            @csrf
            @if(isset($pledge)) @method('PUT') @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="event_id" class="form-label">Event</label>
                    <select class="form-select" id="event_id" name="event_id" required>
                        <option value="">Select Event</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" 
                                {{ (isset($pledge) && $pledge->event_id == $event->id) ? 'selected' : '' }}>
                                {{ $event->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="guest_id" class="form-label">Guest</label>
                    <select class="form-select" id="guest_id" name="guest_id" required>
                        <option value="">Select Guest</option>
                        @foreach($guests as $guest)
                            <option value="{{ $guest->id }}"
                                {{ (isset($pledge) && $pledge->guest_id == $guest->id) ? 'selected' : '' }}>
                                {{ $guest->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="type" class="form-label">Pledge Type</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="cash" {{ (isset($pledge) && $pledge->type == 'cash') ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ (isset($pledge) && $pledge->type == 'bank_transfer') ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="service" {{ (isset($pledge) && $pledge->type == 'service') ? 'selected' : '' }}>Service</option>
                        <option value="gift" {{ (isset($pledge) && $pledge->type == 'gift') ? 'selected' : '' }}>Gift</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                           value="{{ $pledge->amount ?? old('amount') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="2">{{ $pledge->description ?? old('description') }}</textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="deadline" class="form-label">Deadline</label>
                    <input type="date" class="form-control" id="deadline" name="deadline" 
                           value="{{ isset($pledge) ? $pledge->deadline->format('Y-m-d') : old('deadline') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Options</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" 
                               {{ (isset($pledge) && $pledge->is_anonymous) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_anonymous">Anonymous Pledge</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring"
                               {{ (isset($pledge) && $pledge->is_recurring) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_recurring">Recurring Pledge</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Pledge</button>
            <a href="{{ route('pledges.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>