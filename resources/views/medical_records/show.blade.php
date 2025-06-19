@extends('adminlte::page')

@section('title', __('View Medical Record'))

@section('content_header')
    <h1>{{ __('View Medical Record') }}</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ auth()->user()->type != 'admin' ? 'My' : '' }} {{ __('Medical Record Details') }}</h3>
            <a href="{{ route('medical-records.index') }}" class="btn btn-sm btn-secondary float-right">{{ __('Back to list') }}</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    @if(auth()->user()->type != 'patient')
                        <tr>
                            <th>{{ __('Patient') }}</th>
                            <td>{{ $medicalRecord->patient->name }}</td>
                        </tr>
                    @endif
                    @if(auth()->user()->type != 'doctor')
                        <tr>
                            <th>{{ __('Doctor') }}</th>
                            <td>{{ $medicalRecord->doctor->user->name }} - {{ $medicalRecord->doctor->specialization }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <td>{{ $medicalRecord->date }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Diagnosis') }}</th>
                        <td>{{ $medicalRecord->diagnosis }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Treatment') }}</th>
                        <td>{{ $medicalRecord->treatment }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Prescription') }}</th>
                        <td>{{ $medicalRecord->prescription }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Created At') }}</th>
                        <td>{{ $medicalRecord->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Updated At') }}</th>
                        <td>{{ $medicalRecord->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
