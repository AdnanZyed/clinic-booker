@extends('adminlte::page')

@section('title', __('My Patients'))

@section('content_header')
    <h1>{{ __('My Patients') }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('List of My Patients') }}</h3>
        </div>
        <div class="card-body">
            <table id="patients-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($patients as $patient)
                        <tr>
                            <td>{{ $patient->id }}</td>
                            <td>{{ $patient->name }}</td>
                            <td>{{ $patient->email }}</td>
                            <td>
                                <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
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
        $(document).ready(function () {
            $('#patients-table').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                columnDefs: [{
                    targets: 3,
                    searchable: false,
                    orderable: false
                }]
            });
        });

        @if (session('success'))
            toastr.success('{{ session('success') }}');
        @endif
    </script>
@stop
