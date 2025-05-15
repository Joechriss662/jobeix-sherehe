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

                {{-- Event --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('Event') }}</label>
                    <input type="text" class="form-control" value="{{ $invitation->event->name }}" disabled>
                </div>

                {{-- Guest --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('Guest') }}</label>
                    <input type="text" class="form-control" value="{{ $invitation->guest->name }}" disabled>
                </div>

                {{-- Invitation Code --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('Invitation Code') }}</label>
                    <input type="text" class="form-control" value="{{ $invitation->code }}" disabled>
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <label for="status" class="form-label">{{ __('Status') }}</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="pending" {{ $invitation->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ $invitation->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="declined" {{ $invitation->status == 'declined' ? 'selected' : '' }}>Declined</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('invitations.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
