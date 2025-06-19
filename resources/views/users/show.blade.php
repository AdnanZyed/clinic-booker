@extends('adminlte::page')

@section('title', __('View User'))

@section('content_header')
    <h1>{{ __('View') . ' ' . $user->name }}</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="userTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info"
                        role="tab">{{ __('Info') }}</a>
                </li>
                @if ($user->type == 'patient')
                    <li class="nav-item">
                        <a class="nav-link" id="appointments-tab" data-toggle="tab" href="#appointments"
                            role="tab">{{ __('Appointments') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="records-tab" data-toggle="tab" href="#records"
                            role="tab">{{ __('Medical Records') }}</a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="userTabsContent">
                <div class="tab-pane fade show active" id="info" role="tabpanel">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Email') }}</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            @if (auth()->user()->type == 'admin')
                                <tr>
                                    <th>{{ __('Type') }}</th>
                                    <td>{{ ucfirst($user->type) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Created At') }}</th>
                                    <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Updated At') }}</th>
                                    <td>{{ $user->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endif

                            @if ($user->type == 'doctor')
                                <tr>
                                    <th>{{ __('Specialization') }}</th>
                                    <td>{{ $doctor->specialization }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Qualifications') }}</th>
                                    <td>{{ $doctor->qualifications }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Available Days') }}</th>
                                    <td>{{ implode(', ', json_decode($doctor->available_days) ?: []) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Session Duration') }}</th>
                                    <td>{{ $doctor->session_duration }} min</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Total Appointments') }}</th>
                                    <td>{{ $doctor->appointments()->count() }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if ($user->type == 'patient')
                    <div class="tab-pane fade" id="appointments" role="tabpanel">
                        @if ($user->appointments->isEmpty())
                            <p>{{ __('No appointments found.') }}</p>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        @if (auth()->user()->type != 'doctor')
                                            <th>{{ __('Doctor') }}</th>
                                        @endif
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Time') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user->appointments->sortByDesc('date') as $appointment)
                                        @php
                                            if (
                                                auth()->user()->type == 'doctor' &&
                                                $appointment->doctor->user->id != auth()->user()->doctor->id
                                            ) {
                                                continue;
                                            }
                                        @endphp
                                        <tr>
                                            @if (auth()->user()->type != 'doctor')
                                                <td>{{ $appointment->doctor->user->name ?? '-' }}</td>
                                            @endif
                                            <td>{{ $appointment->date }}</td>
                                            <td>{{ $appointment->start_time . ' - ' . $appointment->end_time }}</td>
                                            <td>
                                                @php
                                                    $colors = [
                                                        'pending' => 'warning',
                                                        'confirmed' => 'primary',
                                                        'cancelled' => 'danger',
                                                        'completed' => 'success',
                                                    ];
                                                    $color = $colors[$appointment->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge badge-{{ $color }}">
                                                    {{ ucfirst(string: $appointment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('appointments.show', $appointment->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('appointments.edit', $appointment->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('appointments.destroy', $appointment->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="records" role="tabpanel">
                        @if ($user->medicalRecords->isEmpty())
                            <p>{{ __('No medical records found.') }}</p>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Doctor') }}</th>
                                        <th>{{ __('Diagnosis') }}</th>
                                        <th>{{ __('Notes') }}</th>
                                        <th>{{ __('Created At') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user->medicalRecords->sortByDesc('created_at') as $record)
                                        <tr>
                                            <td>{{ $record->doctor->user->name ?? '-' }}</td>
                                            <td>{{ $record->diagnosis }}</td>
                                            <td>{{ $record->notes }}</td>
                                            <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                                            <td>

                                                @php
                                                    $canEdit =
                                                        auth()->user()->type === 'admin' ||
                                                        (auth()->user()->type === 'doctor' &&
                                                            $record->doctor->user_id === auth()->id());
                                                @endphp
                                                @if ($canEdit)
                                                    <a href="{{ route('medical-records.show', $record->id) }}"
                                                        class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('medical-records.edit', $record->id) }}"
                                                        class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('medical-records.destroy', $record->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
