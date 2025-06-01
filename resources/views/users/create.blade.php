@extends('adminlte::page')

@section('title', __('Create User'))

@section('content_header')
    <h1>{{ __('Create User') }}</h1>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('Add new user') }}</h3>
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary text-end">{{ __('Back to list') }}</a>
    </div>

    <form action="{{ route('users.store') }}" method="POST" novalidate>
        @csrf
        <div class="card-body">

            <div class="form-group">
                <label for="type">{{ __('User Type') }}</label>
                <select name="type" id="type"
                    class="form-control @error('type') is-invalid @enderror" required>
                    <option value="patient" {{ old('type') == 'patient' ? 'selected' : '' }}>{{ __('Patient') }}</option>
                    <option value="doctor" {{ old('type') == 'doctor' ? 'selected' : '' }}>{{ __('Doctor') }}</option>
                    <option value="admin" {{ old('type') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                </select>
                @error('type')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="name">{{ __('Name') }}</label>
                <input type="text" name="name" id="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">{{ __('Email') }}</label>
                <input type="email" name="email" id="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" required>
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">{{ __('Password') }}</label>
                <input type="password" name="password" id="password"
                    class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="form-control" required>
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
        </div>
    </form>
</div>
@stop
