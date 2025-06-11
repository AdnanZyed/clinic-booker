@extends('adminlte::page')

@section('title', __('Create User'))

@section('content_header')
    <h1>{{ __('Create User') }}</h1>
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Add new user') }}</h3>
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary float-right">{{ __('Back to list') }}</a>
        </div>

        <form action="{{ route('users.store') }}" method="POST" novalidate>
            @csrf
            <div class="card-body">

                <div class="form-group">
                    <label for="type">{{ __('User Type') }}</label>
                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="patient" {{ old('type') == 'patient' ? 'selected' : '' }}>{{ __('Patient') }}
                        </option>
                        <option value="doctor" {{ old('type') == 'doctor' ? 'selected' : '' }}>{{ __('Doctor') }}
                        </option>
                        <option value="admin" {{ old('type') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                    </select>
                    @error('type')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
                    <input type="email" name="email" id="email"
                        class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div id="doctor-fields" style="display: none;">
                    <div class="form-group">
                        <label for="specialization">{{ __('Specialization') }}</label>
                        <input type="text" name="specialization" id="specialization" value="{{ old('specialization') }}" class="form-control @error('specialization') is-invalid @enderror">
                        @error('specialization')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="qualifications">{{ __('Qualifications') }}</label>
                        <input type="text" name="qualifications" id="qualifications" value="{{ old('qualifications') }}" class="form-control @error('qualifications') is-invalid @enderror">
                        @error('qualifications')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="available_days">{{ __('Available Days') }}</label>
                        <select name="available_days[]" id="available_days" class="form-control @error('available_days') is-invalid @enderror" multiple>
                            <option value="Sunday">{{ __('Sunday') }}</option>
                            <option value="Monday">{{ __('Monday') }}</option>
                            <option value="Tuesday">{{ __('Tuesday') }}</option>
                            <option value="Wednesday">{{ __('Wednesday') }}</option>
                            <option value="Thursday">{{ __('Thursday') }}</option>
                            <option value="Friday">{{ __('Friday') }}</option>
                            <option value="Saturday">{{ __('Saturday') }}</option>
                        </select>
                        @error('available_days')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="session_duration">{{ __('Session Duration (minutes)') }}</label>
                        <input type="number" name="session_duration" id="session_duration" value="{{ old('session_duration') }}" class="form-control @error('session_duration') is-invalid @enderror">
                        @error('session_duration')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
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
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                        required>
                </div>

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
            </div>
        </form>
    </div>
@stop

@push('js')
    <script>
        $('#type').on('change', function() {
            if ($(this).val() === 'doctor') {
                $('#doctor-fields').show();
            } else {
                $('#doctor-fields').hide();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const doctorFields = document.getElementById('doctor-fields');

            const doctorInputs = [
                document.getElementById('specialization'),
                document.getElementById('qualifications'),
                document.getElementById('available_days'),
                document.getElementById('session_duration')
            ];

            function toggleDoctorFields() {
                const isDoctor = typeSelect.value === 'doctor';

                doctorFields.style.display = isDoctor ? 'block' : 'none';

                doctorInputs.forEach(input => {
                    if (isDoctor) {
                        input.setAttribute('required', 'required');
                    } else {
                        input.removeAttribute('required');
                    }
                });
            }

            typeSelect.addEventListener('change', toggleDoctorFields);
            toggleDoctorFields();
        });
    </script>
@endpush
