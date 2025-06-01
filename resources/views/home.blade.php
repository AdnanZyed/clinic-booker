@extends('layouts.app')

@section('page_title', 'لوحة التحكم')

@section('content_inner')

    {{-- للمسؤول (Admin) --}}
    @if (auth()->user()->type === 'admin')
        <div class="card bg-light mb-3">
            <div class="card-header">لوحة المشرف</div>
            <div class="card-body">
                <p>مرحباً أيها المشرف {{ auth()->user()->name }}</p>
                <a href="{{ route('doctors.index') }}" class="btn btn-primary">إدارة الأطباء</a>
                {{-- <a href="{{ route('users.index') }}" class="btn btn-secondary">إدارة المستخدمين</a> --}}
            </div>
        </div>
    @endif

    {{-- للطبيب (Doctor) --}}
    @if (auth()->user()->type === 'doctor')
        <div class="card bg-light mb-3">
            <div class="card-header">لوحة الطبيب</div>
            <div class="card-body">
                <p>مرحباً د. {{ auth()->user()->name }}</p>
                <a href="{{ route('appointments.index') }}" class="btn btn-info">مواعيدي</a>
                <a href="{{ route('medical-records.index') }}" class="btn btn-warning">سجلات المرضى</a>
            </div>
        </div>
    @endif

    {{-- للمريض (Patient) --}}
    @if (auth()->user()->type === 'patient')
        <div class="card bg-light mb-3">
            <div class="card-header">لوحة المريض</div>
            <div class="card-body">
                <p>مرحباً {{ auth()->user()->name }}</p>
                <a href="{{ route('appointments.create') }}" class="btn btn-success">احجز موعد</a>
                <a href="{{ route('medical-records.index') }}" class="btn btn-outline-primary">سجلي الطبي</a>
            </div>
        </div>
    @endif

@endsection
