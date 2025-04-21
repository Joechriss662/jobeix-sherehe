@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Contributions for Pledge #{{ $pledge->id }}</h2>

    <a href="{{ route('pledges.contributions.create', $pledge) }}" class="btn btn-primary mb-3">Add Contribution</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Amount</th>
                <th>Method</th>
                <th>Transaction Ref</th>
                <th>Receipt</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contributions as $contribution)
                <tr>
                    <td>${{ number_format($contribution->amount, 2) }}</td>
                    <td>{{ $contribution->method }}</td>
                    <td>{{ $contribution->transaction_reference }}</td>
                    <td>
                        @if($contribution->receipt_path)
                            <a href="{{ asset('storage/' . $contribution->receipt_path) }}" target="_blank">View</a>
                        @endif
                    </td>
                    <td>{{ $contribution->payment_date->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('pledges.contributions.edit', [$pledge, $contribution]) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('pledges.contributions.destroy', [$pledge, $contribution]) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this contribution?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
