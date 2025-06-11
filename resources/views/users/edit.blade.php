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
                    <label for="type">{{ __('User Type') }}</label>
                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="patient" {{ old('type', $user->type) == 'patient' ? 'selected' : '' }}>
                            {{ __('Patient') }}</option>
                        <option value="doctor" {{ old('type', $user->type) == 'doctor' ? 'selected' : '' }}>
                            {{ __('Doctor') }}</option>
                        <option value="admin" {{ old('type', $user->type) == 'admin' ? 'selected' : '' }}>
                            {{ __('Admin') }}</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
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

                @php
                    $doctor = $user->doctor;
                @endphp

                <div id="doctor-fields" style="{{ old('type', $user->type) === 'doctor' ? '' : 'display:none;' }}">
                    <div class="form-group mb-3">
                        <label for="specialization">{{ __('Specialization') }}</label>
                        <input type="text" name="specialization" id="specialization"
                            class="form-control @error('specialization') is-invalid @enderror"
                            value="{{ old('specialization', $doctor->specialization ?? '') }}">
                        @error('specialization')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="qualifications">{{ __('Qualifications') }}</label>
                        <input name="qualifications" id="qualifications"
                            class="form-control @error('qualifications') is-invalid @enderror"
                            value="{{ old('qualifications', $doctor->qualifications ?? '') }}">
                        @error('qualifications')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="available_days">{{ __('Available Days') }}</label>
                        @php
                            $selectedDays = old('available_days', json_decode($doctor->available_days ?? '[]', true));
                        @endphp
                        <select name="available_days[]" id="available_days" class="form-control" multiple>
                            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                <option value="{{ $day }}" {{ in_array($day, $selectedDays) ? 'selected' : '' }}>
                                    {{ __($day) }}
                                </option>
                            @endforeach
                        </select>
                        @error('available_days')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="session_duration">{{ __('Session Duration (minutes)') }}</label>
                        <input type="number" name="session_duration" id="session_duration"
                            class="form-control @error('session_duration') is-invalid @enderror"
                            value="{{ old('session_duration', $doctor->session_duration ?? '') }}">
                        @error('session_duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
            </div>
        </form>

    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const doctorFields = document.getElementById('doctor-fields');

            const doctorInputs = [
                document.getElementById('specialization'),
                document.getElementById('qualifications'),
                document.getElementById('available_days'),
                document.getElementById('session_duration')
            ];

            function toggleDoctorFields() {
                doctorFields.style.display = '{{ $user->type }}' === 'doctor' ? 'block' : 'none';

                doctorInputs.forEach(input => {
                    if ('{{ $user->type }}' === 'doctor') {
                        input.setAttribute('required', 'required');
                    } else {
                        input.removeAttribute('required');
                    }
                });
            }

            toggleDoctorFields();
        });
    </script>
@endsection
