@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pledge Details</h5>
                <div>
                    <a href="{{ route('pledges.edit', $pledge) }}" class="btn btn-sm btn-warning">Edit</a>
                    <a href="{{ route('pledges.contributions.create', $pledge) }}" class="btn btn-sm btn-success">Add Contribution</a>
                    <a href="{{ route('events.show', $pledge->event->id) }}" class="btn btn-primary btn-sm">Back to Event</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Guest:</strong> {{ $pledge->guest->name }}</p>
                        <p><strong>Event:</strong> {{ $pledge->event->name }}</p>
                        <p><strong>Type:</strong> {{ ucfirst($pledge->type) }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Amount:</strong> ${{ number_format($pledge->amount, 2) }}</p>
                        <p><strong>Status:</strong>
                            <span class="badge bg-{{ [
                                'pending' => 'warning',
                                'partially_fulfilled' => 'info',
                                'fulfilled' => 'success',
                                'overdue' => 'danger'
                            ][$pledge->status] }}">
                                {{ ucfirst(str_replace('_', ' ', $pledge->status)) }}
                            </span>
                        </p>
                        <p><strong>Deadline:</strong> {{ $pledge->deadline ? $pledge->deadline->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>
                @if($pledge->description)
                    <div class="mt-3">
                        <strong>Description:</strong>
                        <p>{{ $pledge->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Contributions</h5>
            </div>
            <div class="card-body">
                @if($pledge->contributions->isEmpty())
                    <p class="text-muted">No contributions yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Reference</th>
                                    <th>Receipt</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pledge->contributions as $contribution)
                                <tr>
                                    <td>{{ $contribution->payment_date ? $contribution->payment_date->format('M d, Y') : 'N/A' }}</td>
                                    <td>${{ number_format($contribution->amount, 2) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $contribution->method)) }}</td>
                                    <td>{{ $contribution->transaction_reference ?? 'N/A' }}</td>
                                    <td>
                                        @if($contribution->receipt_path)
                                            <a href="{{ Storage::url($contribution->receipt_path) }}" target="_blank">View</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('pledges.contributions.edit',[$pledge->id, $contribution->id]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('pledges.contributions.destroy', [$pledge->id, $contribution->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this contribution?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Pledge Progress</h5 </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: {{ $pledge->completion_percentage }}%">
                            {{ round($pledge->completion_percentage, 1) }}%
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Pledged</h6>
                                <h4>${{ number_format($pledge->amount, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Paid</h6>
                                <h4>${{ number_format($pledge->contributions->sum('amount'), 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6>Remaining</h6>
                            <h4>${{ number_format($pledge->remaining_balance, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 