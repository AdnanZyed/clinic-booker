@extends('adminlte::page')

@section('title', __('View User'))

@section('content_header')
    <h1>{{ __('View User') }}</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('User Details') }}</h3>
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary text-end">{{ __('Back to list') }}</a>
    </div>
    <div class="card-body">
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
            </tbody>
        </table>
    </div>
</div>
@endsection
