@extends('adminlte::page')

@section('title', __('Appointments'))

@section('content_header')
    <h1>{{ __('Appointments') }}</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1060;">
            <div id="successToast"
                class="alert alert-success alert-dismissible toast align-items-center text-bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('All Appointments') }}</h3>
            <div class="card-tools">
                <a href="{{ route('appointments.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-appointment-plus"></i> {{ __('Add New Appointments') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="appointments-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Patient') }}</th>
                        <th>{{ __('Doctor') }}</th>
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
                            <td>
                                @foreach ($patients as $patient)
                                    {{ $appointment->patient_id == $patient->id ? $patient->name : '' }}
                                @endforeach
                            </td>
                            <td>
                                @foreach ($doctors as $doctor)
                                    {{ $appointment->doctor_id == $doctor->id ? $doctor->user->name : '' }}
                                @endforeach
                            </td>
                            <td>{{ $appointment->date }}</td>
                            <td>{{ $appointment->start_time }}</td>
                            <td>{{ $appointment->end_time }}</td>
                            <td>
                                @php $colors = ['pending'   => 'warning', 'confirmed' => 'primary', 'cancelled' => 'danger', 'completed' => 'success',]; $color = $colors[$appointment->status] ?? 'secondary'; @endphp
                                <span class="badge badge-{{ $color }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td>{{ $appointment->notes }}</td>
                            <td>
                                <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> {{ __('Show') }}
                                </a>
                                <a href="{{ route('appointments.edit', $appointment->id) }}"
                                    class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                                </a>
                                <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> {{ __('Delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
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
                order: [[0, 'desc']]
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
    </script>
@stop
