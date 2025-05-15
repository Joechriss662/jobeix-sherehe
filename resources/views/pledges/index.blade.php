@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Pledges</h5>
        <a href="{{ route('pledges.create') }}" class="btn btn-primary">Create New Pledge</a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Event</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pledges as $pledge)
                    <tr>
                        
                        <td>{{ $pledge->guest->name }}</td>
                        <td>{{ $pledge->event->name }}</td>
                        <td>${{ number_format($pledge->amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ [
                                'pending' => 'warning',
                                'partially_fulfilled' => 'info',
                                'fulfilled' => 'success',
                                'overdue' => 'danger'
                            ][$pledge->status] }}">
                                {{ ucfirst(str_replace('_', ' ', $pledge->status)) }}
                            </span>
                        </td>
                        <td>{{ $pledge->deadline->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('pledges.show', $pledge) }}" class="btn btn-sm btn-info">View</a>
                            <a href="{{ route('pledges.edit', $pledge) }}" class="btn btn-sm btn-warning">Edit</a>
                          <!--  <form action="{{ route('pledges.destroy', $pledge) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this pledge?');">Delete</button>
                            </form>-->
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $pledges->links() }} <!-- Pagination links -->
        </div>
    </div>
</div>
@endsection