@extends('adminlte::page')

@section('title', __('Users'))

@section('content_header')
    <h1>{{ __('Users') }}</h1>
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
            <h3 class="card-title">{{ __('All Users') }}</h3>
            <div class="card-tools">
                <a href="{{ route('users.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-user-plus"></i> {{ __('Add New User') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button class="btn btn-outline-secondary filter-btn active" data-filter="">{{ __('All') }}</button>
                <button class="btn btn-outline-primary filter-btn" data-filter="admin">{{ __('Admins') }}</button>
                <button class="btn btn-outline-success filter-btn" data-filter="doctor">{{ __('Doctors') }}</button>
                <button class="btn btn-outline-warning filter-btn" data-filter="patient">{{ __('Patients') }}</button>
            </div>
            <table id="users-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->type }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> {{ __('Show') }}
                                </a>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                                </a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('{{ __('Are you sure?') }}');">
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
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@stop

@section('js')
    <!-- jQuery + DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#users-table').DataTable({
                responsive: true,
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                    targets: 5,
                    searchable: false,
                    orderable: false
                }]
            });

            $('.filter-btn').on('click', function() {
                var filterValue = $(this).data('filter');

                table.column(2).search(filterValue).draw();

                $('.filter-btn').removeClass('active');

                $(this).addClass('active');

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
