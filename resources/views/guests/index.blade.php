@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('All Guests') }}</h5>
            {{-- Assuming you have a route for creating guests --}}
            <!--<a href="{{ route('guests.create-independent') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> {{ __('Create New Guest') }}
            </a>-->
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Event') }}</th> {{-- Added Event column --}}
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($guests as $guest)
                            <tr>
                                <td>{{ $guest->name }}</td>
                                <td>{{ $guest->phone ?? 'N/A' }}</td> {{-- Display N/A if phone is null --}}
                                <td>{{ $guest->email ?? 'N/A' }}</td> {{-- Display N/A if email is null --}}
                                <td>{{ $guest->event->name ?? 'N/A' }}</td> {{-- Display Event Name, N/A if no event --}}
                                <td>
                                    <a href="{{ route('guests.edit', $guest->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> {{ __('Edit') }}</a>
                                    <form action="{{ route('guests.destroy', $guest->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this guest?');"><i class="fas fa-trash"></i> {{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">{{ __('No guests found.') }}</td> {{-- Adjusted colspan --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($guests instanceof \Illuminate\Pagination\LengthAwarePaginator && $guests->hasPages())
                <div class="mt-3">
                    {{ $guests->links() }} {{-- Pagination links --}}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection