@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Contribution #{{ $contribution->receipt_number }}</h2>

    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Amount:</strong> ${{ number_format($contribution->amount, 2) }}</li>
        <li class="list-group-item"><strong>Method:</strong> {{ $contribution->method }}</li>
        @if($contribution->transaction_reference)
        <li class="list-group-item"><strong>Transaction Reference:</strong> {{ $contribution->transaction_reference }}</li>
        @endif
        <li class="list-group-item"><strong>Payment Date:</strong> {{ $contribution->payment_date->format('Y-m-d') }}</li>
        <li class="list-group-item"><strong>Notes:</strong> {{ $contribution->notes }}</li>
        <li class="list-group-item">
            <strong>Receipt:</strong>
            @if($contribution->receipt_path)
                <a href="{{ asset('storage/' . $contribution->receipt_path) }}" target="_blank">View Receipt</a>
            @else
                No receipt uploaded
            @endif
        </li>
    </ul>

    <a href="{{ route('pledges.contributions.index', $pledge) }}" class="btn btn-secondary">Back to Contributions</a>
</div>
@endsection
