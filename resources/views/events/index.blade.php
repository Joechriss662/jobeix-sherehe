@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Events</h2>
        <a href="{{ route('events.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Event
        </a>
    </div>

    <!-- Search Bar -->
    <form method="GET" action="{{ route('events.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search events by name or location..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
    </form>

    <div class="card shadow-lg">
        <div class="card-body">
            @if($events->isEmpty())
                <p class="text-center text-muted">No events found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Description</th>
                                <th>Start Time</th>
                                <th>Capacity</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($events as $event)
                                <tr>
                                    <td>{{ $event->name }}</td>
                                    <td>{{ $event->location }}</td>
                                    <td>{{ Str::limit($event->description, 50, '...') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($event->start_time)->format('F d, Y h:i A') }}</td>
                                    <td>{{ $event->capacity }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('events.show', $event->id) }}" class="btn btn-info btn-sm">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="{{ route('events.edit', $event->id) }}" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
