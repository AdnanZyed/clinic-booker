@extends('layouts.app')

@section('page_title', __('Dashboard'))

@section('content_inner')

    @if (auth()->user()->type === 'admin')
        <div class="card bg-light mb-3">
            <div class="card-header">{{__('Admin Dashboard')}}</div>
            <div class="card-body">
                <p>{{__('Hello!')}} {{ auth()->user()->name }}</p>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('Manage Users') }}</a>
            </div>
        </div>
    @endif

    @if (auth()->user()->type === 'doctor')
        <div class="card bg-light mb-3">
            <div class="card-header">{{__('Doctor Dashboard')}}</div>
            <div class="card-body">
                <p>مرحباً د. {{ auth()->user()->name }}</p>
                <a href="{{ route('appointments.index') }}" class="btn btn-info">{{__('Appointments')}}</a>
                <a href="{{ route('medical-records.index') }}" class="btn btn-warning">سجلات المرضى</a>
            </div>
        </div>
    @endif

    @if (auth()->user()->type === 'patient')
        <div class="card bg-light mb-3">
            <div class="card-header">{{__('Patient Dashboard')}}</div>
            <div class="card-body">
                <p>مرحباً {{ auth()->user()->name }}</p>
                <a href="{{ route('appointments.create') }}" class="btn btn-success">احجز موعد</a>
                <a href="{{ route('medical-records.index') }}" class="btn btn-outline-primary">سجلي الطبي</a>
            </div>
        </div>
    @endif

@endsection
