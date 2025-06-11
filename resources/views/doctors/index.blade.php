@extends('adminlte::page')

@section('title', __('Doctors'))

@section('content_header')
    <h1>{{ __('Doctors') }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('All Doctors') }}</h3>
            <div class="card-tools">
                <a href="{{ route('doctors.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-doctor-plus"></i> {{ __('Add New Doctor') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="doctors-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                       <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Specialization') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($doctors as $doctor)
                        <tr>
                            <td>{{ $doctor->user->name }}</td>
                            <td>{{ $doctor->user->email }}</td>
                            <td>{{ $doctor->specialization }}</td>
                            <td>
                                <a href="{{ route('doctors.show', $doctor->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> {{ __('Show') }}
                                </a>
                                <a href="{{ route('doctors.edit', $doctor->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                                </a>
                                <form action="{{ route('doctors.destroy', $doctor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('{{ __('Are you sure?') }}')" class="btn btn-sm btn-danger">
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
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@stop

@section('js')
    <!-- jQuery + DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#doctors-table').DataTable({
                responsive: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json"
                },
                columnDefs: [
                    {
                        targets: 4,
                        searchable: false,
                        orderable: false
                    }
                ]
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            var toastEl = document.getElementById('successToast');
            if (toastEl) {
                var toast = new bootstrap.Toast(toastEl, { delay: 3000 }); // 3 ثواني ثم يختفي
                toast.show();
            }
        });
    </script>
@stop
