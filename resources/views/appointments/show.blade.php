@extends('adminlte::page')

@section('title', __('Appointment Details'))

@section('content_header')
    <h1>{{ __('Appointment Details') }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('Appointment Info') }}</h3>
        <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-secondary float-right">{{ __('Back to list') }}</a>
    </div>

    <div class="card-body">
        <p><strong>{{ __('Patient') }}:</strong> {{ $appointment->patient->name ?? '-' }}</p>
        <p><strong>{{ __('Doctor') }}:</strong> {{ $appointment->doctor->user->name ?? '-' }} - ({{ $appointment->doctor->specialization ?? '' }})</p>
        <p><strong>{{ __('Date') }}:</strong> {{ $appointment->date }}</p>
        <p><strong>{{ __('Start Time') }}:</strong> {{ $appointment->start_time }}</p>
        <p><strong>{{ __('End Time') }}:</strong> {{ $appointment->end_time }}</p>
        <p>
            <strong>{{ __('Status') }}:</strong>
            @php $colors = ['pending'   => 'warning', 'confirmed' => 'primary', 'cancelled' => 'danger', 'completed' => 'success',]; $color = $colors[$appointment->status] ?? 'secondary'; @endphp
            <span class="badge badge-{{ $color }}">
                {{ ucfirst($appointment->status) }}
            </span>
        </p>
        <p><strong>{{ __('Notes') }}:</strong> {{ $appointment->notes }}</p>
    </div>
</div>
@stop