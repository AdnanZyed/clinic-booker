@extends('layouts.app')

@section('title', __('Notifications'))

@section('content_header')
    <h1>{{ __('Notifications') }}</h1>
@stop

@section('content')
<div class="container">
    @forelse ($notifications as $notification)
        <div class="card mb-3 {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
            <div class="card-body">
                <h5 class="card-title">{{ $notification->data['title'] }}</h5>
                <p class="card-text">{{ $notification->data['body'] }}</p>
                <p class="card-text">
                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                </p>
                <a href="{{ route('notifications.read', $notification->id) }}" class="btn btn-sm btn-outline-primary">
                    {{__('View')}}
                </a>
            </div>
        </div>
    @empty
        <p>{{__('No Notifications')}}</p>
    @endforelse
</div>
@endsection
