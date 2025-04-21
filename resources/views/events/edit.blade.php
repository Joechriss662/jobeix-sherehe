@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0">Edit Event</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('events.update', $event->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Event Name -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Event Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $event->name) }}" required>
                </div>

                <!-- Location -->
                <div class="mb-3">
                    <label for="location" class="form-label fw-bold">Location</label>
                    <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $event->location) }}" required>
                </div>

                <!-- Capacity -->
                <div class="mb-3">
                    <label for="capacity" class="form-label fw-bold">Capacity</label>
                    <input type="number" name="capacity" id="capacity" class="form-control" value="{{ old('capacity', $event->capacity) }}" min="1" required>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter event description">{{ old('description', $event->description) }}</textarea>
                </div>

                <!-- Start Time -->
                <div class="mb-3">
                    <label for="start_time" class="form-label fw-bold">Start Time</label>
                    <input type="datetime-local" name="start_time" id="start_time" class="form-control" value="{{ \Carbon\Carbon::parse($event->start_time)->format('Y-m-d\TH:i') }}" required>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Update Event
                    </button>
                    <a href="{{ route('events.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
