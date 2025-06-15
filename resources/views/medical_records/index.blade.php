@extends('layouts.app')

@section('title', __('Medical Records'))

@section('content_header')
    <h1>{{ __('Medical Records') }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('All Medical Records') }}</h3>
            <div class="card-tools">
                <a href="{{ route('medical-records.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-medical-records-plus"></i> {{ __('Add New Medical Record') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="records-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Patient') }}</th>
                        <th>{{ __('Doctor') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($medicalRecords as $record)
                        <tr>
                            <td>{{ $record->id }}</td>
                            <td>{{ $record->patient->name }}</td>
                            <td>{{ $record->doctor->user->name ?? '-' }}</td>
                            <td>{{ $record->date }}</td>
                            <td>
                                <a href="{{ route('medical-records.show', $record->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('medical-records.edit', $record->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('medical-records.destroy', $record->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}');">
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
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#records-table').DataTable({
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
    </script>
    @if (session('success'))
        <script>
            toastr.success('{{ session('success') }}');
        </script>
    @endif
@stop
