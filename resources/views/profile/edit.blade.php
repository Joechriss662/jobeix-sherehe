@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8"> {{-- Or col-md-10, col-lg-8 depending on desired width --}}
            <h2 class="mb-4">{{ __('Profile') }}</h2>

            {{-- Update Profile Information --}}
            <div class="card mb-4">
                <div class="card-header">
                    {{ __('Profile Information') }}
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="card mb-4">
                <div class="card-header">
                    {{ __('Update Password') }}
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="card">
                <div class="card-header text-danger fw-bold">
                    {{ __('Delete Account') }}
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
