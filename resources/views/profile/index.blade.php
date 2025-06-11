@extends('adminlte::page')

@section('title', 'الملف الشخصي')

@section('content_header')
    <h1>{{ __('Profile')}}</h1>
@stop

@section('content')

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">{{ __('Profile')}}</div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>{{ __('Name')}}</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label>{{ __('Email')}}</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">{{ __('Update')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
@if (session('success'))
    <script>
        toastr.success('{{ session('success') }}');
    </script>
@endif
@endsection