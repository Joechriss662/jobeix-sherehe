@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Preview SMS</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('invitations.sendSms', $invitation->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="message" class="form-label fw-bold">SMS Message</label>
                    <textarea id="message" name="message" class="form-control" rows="5" required>{{ $message }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">Send SMS</button>
                <a href="{{ route('invitations.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection