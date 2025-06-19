@extends('layouts.app')

@section('title', __('Medical Records'))

@section('content_header')
    <h1>{{ __('Medical Records') }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ auth()->user()->type != 'admin' ? 'My' : '' }} {{ __('Medical Records') }}</h3>
            @if(auth()->user()->type != 'patient')
                <div class="card-tools">
                    <a href="{{ route('medical-records.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-medical-records-plus"></i> {{ __('Add New Medical Record') }}
                    </a>
                </div>
            @endif
        </div>
        <div class="card-body">
            <table id="records-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        @if(auth()->user()->type != 'patient')
                            <th>{{ __('Patient') }}</th>
                        @endif
                        @if(auth()->user()->type != 'doctor')
                            <th>{{ __('Doctor') }}</th>
                        @endif
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($medicalRecords as $record)
                        <tr>
                            <td>{{ $record->id }}</td>
                            @if(auth()->user()->type != 'patient')
                                <td>{{ $record->patient->name }}</td>
                            @endif
                            @if(auth()->user()->type != 'doctor')
                                <td>{{ $record->doctor->user->name ?? '-' }}</td>
                            @endif
                            <td>{{ $record->date }}</td>
                            <td>
                                <a href="{{ route('medical-records.show', $record->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @php
                                    $canEdit = auth()->user()->type === 'admin' ||
                                            (auth()->user()->type === 'doctor' && $record->doctor->user_id === auth()->id());
                                @endphp
                                @if($canEdit)
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
                                @endif
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
            let lastColumnIndex = $('#records-table thead th').length - 1;
            $('#records-table').DataTable({
                responsive: true,
                /* language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json"
                }, */
                columnDefs: [{
                    targets: lastColumnIndex,
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
    
    @if (session('error'))
        <script>
            toastr.error('{{ session('error') }}');
        </script>
    @endif
@stop
