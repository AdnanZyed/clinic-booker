@extends('adminlte::page')

@section('title', __('Appointments'))

@section('content_header')
    <h1>{{ __('Appointments') }}</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-header">
            @if (auth()->user()->type == 'admin')
                <h3 class="card-title">{{ __('All Appointments') }}</h3>
            @else
                <h3 class="card-title">{{ __('My Appointments') }}</h3>
            @endif

            <div class="card-tools">
                <a href="{{ route('appointments.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-appointment-plus"></i> {{ __('Add New Appointment') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="appointments-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        @if (auth()->user()->type != 'patient')
                            <th>{{ __('Patient') }}</th>
                        @endif
                        @if (auth()->user()->type != 'doctor')
                            <th>{{ __('Doctor') }}</th>
                        @endif
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Start Time') }}</th>
                        <th>{{ __('End Time') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Notes') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->id }}</td>
                            @if (auth()->user()->type != 'patient')
                                <td>
                                    @foreach ($patients as $patient)
                                        @php $route = auth()->user()->type == 'admin' ? 'users.show' : 'patients.show'; @endphp
                                        <a href="{{ route($route, $patient->id) }}">
                                            {{ $appointment->patient_id == $patient->id ? $patient->name : '' }}
                                        </a>
                                    @endforeach
                                </td>
                            @endif
                            @if (auth()->user()->type != 'doctor')
                                <td>
                                    @foreach ($doctors as $doctor)
                                        @if (auth()->user()->type != 'patient')
                                            <a href="{{ route('users.show', $doctor->user->id) }}">
                                                {{ $appointment->doctor_id == $doctor->id ? $doctor->user->name : '' }}
                                            </a>
                                        @else
                                            {{ $appointment->doctor_id == $doctor->id ? $doctor->user->name : '' }}
                                        @endif
                                    @endforeach
                                </td>
                            @endif
                            <td>{{ $appointment->date }}</td>
                            <td>{{ $appointment->start_time }}</td>
                            <td>{{ $appointment->end_time }}</td>
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
                                @if(auth()->user()->type != 'patient')
                                    <div class="d-flex align-items-center">
                                        <select name="status" data-id="{{ $appointment->id }}"
                                            class="form-control status text-white bg-{{ $color }}"
                                            style="min-width: 150px;" required>
                                            @foreach (['pending', 'confirmed', 'cancelled', 'completed'] as $status)
                                                <option class="text-black bg-white" value="{{ $status }}"
                                                    {{ $appointment->status === $status ? 'selected' : '' }}>
                                                    {{ ucfirst($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="spinner-border spinner-border-sm d-none ml-2 loading" role="status"
                                            aria-hidden="true"></span>
                                    </div>
                                @else
                                    <span class="badge badge-{{ $color }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $appointment->notes }}</td>
                            <td>
                                <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if ((auth()->user()->type == 'patient') & ($appointment->status == 'pending') || auth()->user()->type != 'patient')
                                    <a href="{{ route('appointments.edit', $appointment->id) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}');">
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
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#appointments-table').DataTable({
                responsive: true,
                /* language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json"
                }, */
                columnDefs: [{
                    targets: 4,
                    searchable: false,
                    orderable: false
                }],
                order: [
                    [0, 'desc']
                ]
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            var toastEl = document.getElementById('successToast');
            if (toastEl) {
                var toast = new bootstrap.Toast(toastEl, {
                    delay: 3000
                });
                toast.show();
            }
        });

        const statusColorMap = {
            pending: 'warning',
            confirmed: 'primary',
            cancelled: 'danger',
            completed: 'success',
        };

        $(document).ready(function() {
            $('.status').on('change', function(e) {
                e.preventDefault();

                const $select = $(this);
                const appointmentId = $select.data('id');
                const newStatus = $select.val();
                const $spinner = $select.closest('td').find('.loading');

                $.ajax({
                    url: `/dashboard/appointments/${appointmentId}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT',
                        status: newStatus
                    },
                    beforeSend: function() {
                        $spinner.removeClass('d-none');
                    },
                    success: function(response) {
                        const successMsg = @json(__('Status updated successfully'));
                        toastr.success(successMsg);

                        const newColor = statusColorMap[newStatus] || 'secondary';
                        $select
                            .removeClass()
                            .addClass(`form-control status text-white bg-${newColor}`);
                    },
                    error: function() {
                        const failMsg = @json(__('Failed to update status'));
                        toastr.error(failMsg);
                    },
                    complete: function() {
                        $spinner.addClass('d-none');
                    }
                });
            });
        });
    </script>
    @if (session('success'))
        <script>
            toastr.success('{{ session('success') }}');
        </script>
    @endif
    @if (session('error'))
        <script>
            toastr.error('{{ session('error') }}');
        </script>
    @endif
@stop
