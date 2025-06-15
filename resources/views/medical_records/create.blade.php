@extends('layouts.app')

@section('title', __('Add Medical Record'))

@section('content_header')
    <h1>{{ __('Create Medical Record') }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Add new Medical Record') }}</h3>
            <a href="{{ route('medical-records.index') }}"
               class="btn btn-sm btn-secondary float-right">{{ __('Back to list') }}</a>
        </div>
        <form action="{{ route('medical-records.store') }}" method="POST">
            @csrf

            <div class="card-body">
                <div class="form-group">
                    <label for="patient_id" class="form-label">{{ __('Patient') }}</label>
                    <select name="patient_id" id="patient_id"
                            class="form-control @error('patient_id') is-invalid @enderror" required>
                        <option value="">{{ __('Choose Patient') }}</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}"
                                {{ old('patient_id', $record->patient_id ?? '') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="doctor_id" class="form-label">{{ __('Doctor') }}</label>
                    <select name="doctor_id" id="doctor_id"
                            class="form-control @error('doctor_id') is-invalid @enderror" required>
                        <option value="">{{ __('Choose Doctor') }}</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}"
                                {{ old('doctor_id', $record->doctor_id ?? '') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->user->name }} - {{ $doctor->specialization }}
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="date" class="form-label">{{ __('Date') }}</label>
                    <input type="date" name="date" id="date"
                           class="form-control @error('date') is-invalid @enderror"
                           value="{{ old('date', $record->date ?? '') }}" required>
                    @error('date')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="diagnosis" class="form-label">{{ __('Diagnosis') }}</label>
                    <textarea name="diagnosis" id="diagnosis"
                              class="form-control @error('diagnosis') is-invalid @enderror">{{ old('diagnosis', $record->diagnosis ?? '') }}</textarea>
                    @error('diagnosis')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="treatment" class="form-label">{{ __('Treatment') }}</label>
                    <textarea name="treatment" id="treatment"
                              class="form-control @error('treatment') is-invalid @enderror">{{ old('treatment', $record->treatment ?? '') }}</textarea>
                    @error('treatment')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="prescription" class="form-label">{{ __('Prescription') }}</label>
                    <textarea name="prescription" id="prescription"
                              class="form-control @error('prescription') is-invalid @enderror">{{ old('prescription', $record->prescription ?? '') }}</textarea>
                    @error('prescription')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
@endsection
