@extends('adminlte::page')

@section('title', 'الملف الشخصي')

@section('content_header')
    <h1>الملف الشخصي</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="col-md-6">
            @include('profile.partials.update-password-form')
        </div>

        <div class="col-md-12 mt-4">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@stop
