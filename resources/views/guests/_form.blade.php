<form id="guestForm" action="{{ route('guests.store', ['event' => $event->id]) }}" method="POST">
    @csrf

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Single Data Fields -->
    <div id="singleDataFields">
        <div class="mb-3">
            <label for="name" class="form-label">Guest Name</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $guest->name ?? '') }}" placeholder="Enter guest name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone (Optional)</label>
            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                   value="{{ old('phone', $guest->phone ?? '') }}" placeholder="Enter phone number">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email (Optional)</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email', $guest->email ?? '') }}" placeholder="Enter email address">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    @if(isset($event))
        <input type="hidden" name="event_id" value="{{ $event->id }}">
    @else
        <div class="mb-3">
            <label for="event_id" class="form-label">Event</label>
            <select name="event_id" id="event_id" class="form-control @error('event_id') is-invalid @enderror">
                <option value="">Select an event</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}" {{ old('event_id', $guest->event_id ?? '') == $event->id ? 'selected' : '' }}>
                        {{ $event->name }}
                    </option>
                @endforeach
            </select>
            @error('event_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @endif

    @if(isset($invitations) && $invitations->isNotEmpty())
        <div class="mb-3">
            <label for="invitation_id" class="form-label">Invitation (Optional)</label>
            <select name="invitation_id" id="invitation_id" class="form-select">
                <option value="">Select an invitation</option>
                @foreach($invitations as $invitation)
                    <option value="{{ $invitation->id }}" 
                            {{ old('invitation_id', $guest->invitation_id ?? '') == $invitation->id ? 'selected' : '' }}>
                        #{{ $invitation->id }} ({{ $invitation->event->name }})
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- Bulk Data Input -->
    <div id="bulkDataFields" style="display: none;">
        <div class="mb-3">
            <label for="bulk_data" class="form-label">Bulk Data Input</label>
            <textarea name="bulk_data" id="bulk_data" class="form-control @error('bulk_data') is-invalid @enderror" 
                      rows="6" placeholder="Enter guest details in bulk (e.g., one guest per line: Name, Phone, Email)"></textarea>
            @error('bulk_data')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <p class="text-muted">Format: Name, Phone, Email (one guest per line)</p>
    </div>

    <!-- Toggle Buttons -->
    <div class="mb-3">
        <button type="button" id="toggleBulkInput" class="btn btn-secondary">Switch to Bulk Input</button>
        <button type="button" id="toggleSingleInput" class="btn btn-secondary" style="display: none;">Switch to Single Input</button>
    </div>

    <!-- Save and Cancel Buttons -->
    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle"></i> Save
        </button>
        <button type="button" class="btn btn-secondary ms-2 cancel-btn">
            <i class="bi bi-x-circle"></i> Cancel
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle between single data fields and bulk input
        const singleDataFields = document.getElementById('singleDataFields');
        const bulkDataFields = document.getElementById('bulkDataFields');
        const toggleBulkInput = document.getElementById('toggleBulkInput');
        const toggleSingleInput = document.getElementById('toggleSingleInput');

        toggleBulkInput.addEventListener('click', function () {
            singleDataFields.style.display = 'none';
            bulkDataFields.style.display = 'block';
            toggleBulkInput.style.display = 'none';
            toggleSingleInput.style.display = 'inline-block';
        });

        toggleSingleInput.addEventListener('click', function () {
            singleDataFields.style.display = 'block';
            bulkDataFields.style.display = 'none';
            toggleBulkInput.style.display = 'inline-block';
            toggleSingleInput.style.display = 'none';
        });

        // Handle cancel button for modals or standalone forms
        document.querySelectorAll('.cancel-btn').forEach(button => {
            button.addEventListener('click', function () {
                const modal = button.closest('.modal');
                if (modal) {
                    const bootstrapModal = bootstrap.Modal.getInstance(modal);
                    bootstrapModal.hide(); // Close the modal
                } else {
                    // Hide the form if it's not in a modal
                    const formContainer = button.closest('.card');
                    if (formContainer) {
                        formContainer.style.display = 'none';
                    }
                }
            });
        });
    });
</script>
