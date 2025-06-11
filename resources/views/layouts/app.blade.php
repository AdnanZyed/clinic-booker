@extends('adminlte::page')

@section('title', config('app.name'))

@section('content_header')
    <h1>@yield('page_title', __('Dashboard'))</h1>
@stop

@section('content')
    @yield('content_inner')
@stop
