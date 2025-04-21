@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Contribution for Pledge: {{ $contribution->pledge->id }}</h2>
    <form action="{{ route('contributions.update', $contribution) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" name="amount" class="form-control" value="{{ $contribution->amount }}" required>
        </div>
        <div class="form-group">
            <label for="payment_date">Payment Date</label>
            <input type="date" name="payment_date" class="form-control" value="{{ $contribution->payment_date }}" required>
        </div>
        <div class="form-group">
            <label for="method">Payment Method</label>
            <input type="text" name="method" class="form-control" value="{{ $contribution->method }}" required>
        </div>
        <div class="form-group">
            <label for="transaction_reference">Transaction Reference</label>
            <input type="text" name="transaction_reference" class="form-control" value="{{ $contribution->transaction_reference }}">
        </div>
        <div class="form-group">
            <label for="receipt">Upload New Receipt (optional)</label>
            <input type="file" name="receipt" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Update Contribution</button>
    </form>
</div>
@endsection