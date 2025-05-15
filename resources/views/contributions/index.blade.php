@extends('layouts.app')

@section('content')
<div class="container py-5">
    @if(isset($events))
        <!-- Event Selection -->
        <h4 class="mb-4 text-center">{{ __('Select Event') }}</h4>
        <form method="GET" action="{{ route('contributions.index') }}">
            <div class="mb-3">
                <label for="event" class="form-label">{{ __('Event') }}</label>
                <select id="event" name="event" class="form-select" required>
                    <option value="">{{ __('-- Select Event --') }}</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">{{ __('View Contributions') }}</button>
        </form>
    @elseif(isset($contributions))
        <!-- Contributions Listing -->
        <h4 class="mb-4 text-center">{{ __('Contributions for Event: ') . $event->name }}</h4>
        @if($contributions->isEmpty())
            <p class="text-center">{{ __('No contributions found for this event.') }}</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Contributor Name') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contributions as $contribution)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $contribution->contributor_name }}</td>
                            <td>{{ $contribution->amount }}</td>
                            <td>{{ $contribution->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif
</div>
@endsection
