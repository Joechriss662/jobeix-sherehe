@extends('layouts.app')

@section('content')
<div class="container py-4"> {{-- Use py-4 for consistent top/bottom padding --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-tachometer-alt me-2"></i> {{ __('Dashboard') }}</h1> {{-- Added icon to title --}}
        {{-- Optional: Add a primary action button here if relevant --}}
    </div>

    <!-- Salutation Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow bg-light border-0"> {{-- Added shadow, bg-light, border-0 --}}
                <div class="card-body text-center py-4">
                    {{-- Refined Salutation Card Style --}}
                    <div class="d-flex align-items-center justify-content-center"> {{-- Use flex to align icon and text, center content --}}
                         <i class="fas fa-hand-wave fa-2x text-primary me-3"></i> {{-- Larger icon, primary color, margin right --}}
                         <div>
                            <h4 class="card-title mb-0">{{ __('Welcome back') }}, <span class="text-primary">{{ Auth::user()->name }}</span>!</h4> {{-- Highlighted name --}}
                            <p class="card-text text-muted mt-1">{{ __("Here's an overview of your system's key metrics.") }}</p> {{-- Slightly refined text --}}
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Salutation Card -->

    <div class="row pt-3">
        <!-- Events Card - Using col-md-6 col-lg-4 for responsive 2 or 3 columns -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div>
                        <div class="mb-2">
                            <i class="fas fa-calendar-alt fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title mb-1">{{ __('Events') }}</h5> {{-- Localized title --}}
                        <p class="display-4 fw-bold text-dark">{{ $eventsCount }}</p> {{-- Ensure count is dark text --}}
                    </div>
                    <div class="mt-auto"> {{-- Push button to the bottom --}}
                        <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View Events') }}</a> {{-- Localized button text --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Guests Card -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div>
                        <div class="mb-2">
                            <i class="fas fa-users fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title mb-1">{{ __('Guests') }}</h5> {{-- Localized title --}}
                        <p class="display-4 fw-bold text-dark">{{ $guestsCount }}</p> {{-- Ensure count is dark text --}}
                    </div>
                    <div class="mt-auto"> {{-- Push button to the bottom --}}
                        <a href="{{ route('guests.index') }}" class="btn btn-sm btn-outline-success">{{ __('View Guests') }}</a> {{-- Localized button text --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Pledges Card -->
        <!--<div class="col-md-6 col-lg-4 mb-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div>
                        <div class="mb-2">
                            <i class="fas fa-hand-holding-usd fa-3x text-warning"></i>
                        </div>
                        <h5 class="card-title mb-1">{{ __('Pledges') }}</h5> {{-- Localized title --}}
                        <p class="display-4 fw-bold text-dark">{{ $pledgesCount }}</p> {{-- Ensure count is dark text --}}
                    </div>
                    <div class="mt-auto"> {{-- Push button to the bottom --}}
                        <a href="{{ route('pledges.index') }}" class="btn btn-sm btn-outline-warning">{{ __('View Pledges') }}</a> {{-- Localized button text --}}
                    </div>
                </div>
            </div>
        </div>-->

        <!-- Contributions Card -->
        <!--<div class="col-md-6 col-lg-4 mb-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div>
                        <div class="mb-2">
                            <i class="fas fa-donate fa-3x text-info"></i>
                        </div>
                        <h5 class="card-title mb-1">{{ __('Contributions') }}</h5> {{-- Localized title --}}
                        <p class="display-4 fw-bold text-dark">{{ $contributionsCount }}</p> {{-- Ensure count is dark text --}}
                    </div>
                    <div class="mt-auto"> {{-- Push button to the bottom --}}
                        <a href="{{ route('contributions.index') }}" class="btn btn-sm btn-outline-info">{{ __('View Contributions') }}</a> {{-- Localized button text --}}
                    </div>
                </div>
            </div>
        </div>-->

        <!-- Invitations Card -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div>
                        <div class="mb-2">
                            <i class="fas fa-envelope-open-text fa-3x text-danger"></i>
                        </div>
                        <h5 class="card-title mb-1">{{ __('Invitations') }}</h5> {{-- Localized title --}}
                        <p class="display-4 fw-bold text-dark">{{ $invitationsCount }}</p> {{-- Ensure count is dark text --}}
                    </div>
                    <div class="mt-auto"> {{-- Push button to the bottom --}}
                        <a href="{{ route('invitations.index') }}" class="btn btn-sm btn-outline-danger">{{ __('View Invitations') }}</a> {{-- Localized button text --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pledges Amount Card -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div>
                        <div class="mb-2">
                            <i class="fas fa-coins fa-3x text-info"></i> {{-- Changed icon and color --}}
                        </div>
                        <h5 class="card-title mb-1">{{ __('Total Pledged') }}</h5>
                        <p class="display-4 fw-bold text-dark">
                            {{-- Format as currency, adjust 'TZS' and locale as needed --}}
                            {{ number_format($totalPledgeAmount, 2) }} <small class="text-muted fs-5">TZS</small>
                        </p>
                    </div>
                    {{-- No "View" button needed for a sum, unless you link to a detailed report --}}
                </div>
            </div>
        </div>

        <!-- Total Contributions Amount Card -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div>
                        <div class="mb-2">
                            <i class="fas fa-hand-holding-heart fa-3x text-success"></i> {{-- Changed icon and color --}}
                        </div>
                        <h5 class="card-title mb-1">{{ __('Total Contributed') }}</h5>
                        <p class="display-4 fw-bold text-dark">
                            {{-- Format as currency, adjust 'TZS' and locale as needed --}}
                            {{ number_format($totalContributionAmount, 2) }} <small class="text-muted fs-5">TZS</small>
                        </p>
                    </div>
                    {{-- No "View" button needed for a sum --}}
                </div>
            </div>
        </div>
    </div> <!-- End .row -->
</div> <!-- End .container -->
@endsection
```
