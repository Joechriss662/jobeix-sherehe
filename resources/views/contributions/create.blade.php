@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Add Contribution to Pledge: {{ $pledge->id }}</h2>

    <!-- Display Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pledges.contributions.store', $pledge->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="amount">Contribution Amount <span class="text-danger">*</span></label>
            <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" required>
            @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="method">Payment Method <span class="text-danger">*</span></label>
            <select name="method" id="method" class="form-control @error('method') is-invalid @enderror" required>
                <option value="">Select Method</option>
                <option value="cash">Cash</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="credit_card">Credit Card</option>
                <option value="other">Other</option>
            </select>
            @error('method')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="transaction_reference">Transaction Reference</label>
            <input type="text" name="transaction_reference" id="transaction_reference" class="form-control @error('transaction_reference') is-invalid @enderror">
            @error('transaction_reference')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="receipt">Upload Receipt (optional)</label>
            <input type="file" name="receipt" id="receipt" class="form-control @error('receipt') is-invalid @enderror">
            @error('receipt')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <input type="hidden" name="pledge_id" value="{{ $pledge->id }}">

        <button type="submit" class="btn btn-primary">Add Contribution</button>
        <a href="{{ route('pledges.show', $pledge) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection