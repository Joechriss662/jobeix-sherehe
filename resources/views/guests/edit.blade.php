@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5>{{ __('Edit Guest') }}</h5>
        </div>
        <div class="card-body">
            <form id="guestForm" method="POST" action="{{ route('guests.update', $guest->id) }}">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $guest->name) }}" required>
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $guest->email) }}">
                </div>

                {{-- Phone --}}
                <div class="mb-3">
                    <label for="phone" class="form-label">{{ __('Phone') }}</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $guest->phone) }}">
                </div>

                {{-- Event --}}
                <div class="mb-3">
                    <label for="event_id" class="form-label">{{ __('Event') }}</label>
                    <select name="event_id" id="event_id" class="form-select" required>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ $guest->event_id == $event->id ? 'selected' : '' }}>
                                {{ $event->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Invitation --}}
                @php
                    $guestInvitation = isset($guest->invitation_id) ? $invitations->where('id', $guest->invitation_id)->first() : null;
                @endphp
                <div class="mb-3">
                    <label class="form-label">{{ __('Invitation') }}</label>
                    @if($guestInvitation)
                        <input type="text" class="form-control" value="{{ $guestInvitation->code ?? $guestInvitation->id }}" disabled>
                        <input type="hidden" name="invitation_id" value="{{ $guestInvitation->id }}">
                    @elseif(isset($invitations) && $invitations->count())
                        <select name="invitation_id" id="invitation_id" class="form-select">
                            <option value="">{{ __('None') }}</option>
                            @foreach($invitations as $invitation)
                                <option value="{{ $invitation->id }}" {{ (old('invitation_id', $guest->invitation_id ?? null) == $invitation->id) ? 'selected' : '' }}>
                                    {{ $invitation->code ?? $invitation->id }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" class="form-control" value="{{ __('No invitations available') }}" disabled>
                    @endif
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('guests.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Guest
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection