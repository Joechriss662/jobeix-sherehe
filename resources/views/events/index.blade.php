@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-header">
      <div class="d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-calendar3 me-2"></i> {{ __('All Events') }}</h4>
        <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-circle me-1"></i> {{ __('Create Event') }}
        </a>
      </div>
    </div>
    <div class="card-body">
      @if($events->isEmpty())
      <p class="text-center text-muted">{{ __('No events found.') }}</p>
      @else
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>{{ __('Name') }}</th>
              <th>{{ __('Location') }}</th>
              <th>{{ __('Start Time') }}</th>
              <th>{{ __('Capacity') }}</th>
              <th class="text-end">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($events as $event)
            <tr>
              <td>{{ $event->name }}</td>
              <td>{{ $event->location }}</td>
              <td>{{ \Carbon\Carbon::parse($event->start_time)->format('D, M j, Y, g:i A') }}</td>
              <td>{{ $event->capacity ?? __('N/A') }}</td>
              <td class="text-end">
                <div class="d-inline-flex gap-2">
                  <a href="{{ route('events.show', $event->id) }}" class="btn btn-info btn-sm">
                    <span class="d-inline d-sm-none"><i class="bi bi-eye"></i></span>
                    <span class="d-none d-sm-inline">View</span>
                  </a>
                  <a href="{{ route('events.edit', $event->id) }}" class="btn btn-warning btn-sm">
                    <span class="d-inline d-sm-none"><i class="bi bi-pencil-square"></i></span>
                    <span class="d-none d-sm-inline">Edit</span>
                  </a>
                  <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this event?')">
                      <span class="d-inline d-sm-none"><i class="bi bi-trash"></i></span>
                      <span class="d-none d-sm-inline">Delete</span>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center mt-4">
        {{ $events->links() }}
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
```
