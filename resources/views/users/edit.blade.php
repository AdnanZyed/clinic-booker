@extends('adminlte::page')

@section('title', __('Edit User'))

@section('content_header')
    <h1>{{ __('Edit User') }}</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('Edit User Information') }}</h3>
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary float-right">{{ __('Back to list') }}</a>
    </div>
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group mb-3">
                <label for="name">{{ __('Name') }}</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                       class="form-control @error('name') is-invalid @enderror" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="email">{{ __('Email') }}</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                       class="form-control @error('email') is-invalid @enderror" required>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="type">{{ __('User Type') }}</label>
                <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                    <option value="patient" {{ old('type', $user->type) == 'patient' ? 'selected' : '' }}>{{ __('Patient') }}</option>
                    <option value="doctor" {{ old('type', $user->type) == 'doctor' ? 'selected' : '' }}>{{ __('Doctor') }}</option>
                    <option value="admin" {{ old('type', $user->type) == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                </select>
                @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
        </div>
    </form>
</div>
@endsection
