<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }}">

    {{-- Sidebar brand logo --}}
    @if(config('adminlte.logo_img_xl'))
        @include('adminlte::partials.common.brand-logo-xl')
    @else
        @include('adminlte::partials.common.brand-logo-xs')
    @endif

    {{-- Sidebar menu --}}
    <div class="sidebar">
        <nav class="pt-2">
            <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                data-widget="treeview" role="menu"
                @if(config('adminlte.sidebar_nav_animation_speed') != 300)
                    data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}"
                @endif
                @if(!config('adminlte.sidebar_nav_accordion'))
                    data-accordion="false"
                @endif>

                @php
                    $user = auth()->user();
                @endphp

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>لوحة التحكم</p>
                    </a>
                </li>

                @if($user && $user->type === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>إدارة المستخدمين</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('appointments.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>إدارة المواعيد</p>
                        </a>
                    </li>
                @elseif($user && $user->type === 'doctor')
                    <li class="nav-item">
                        <a href="{{ route('doctor.appointments') }}" class="nav-link">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>مواعيدي</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('doctor.patients') }}" class="nav-link">
                            <i class="nav-icon fas fa-user-injured"></i>
                            <p>مرضاي</p>
                        </a>
                    </li>
                @elseif($user && $user->type === 'patient')
                    <li class="nav-item">
                        <a href="{{ route('patient.appointments') }}" class="nav-link">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>مواعيدي</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('patient.profile') }}" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>حسابي</p>
                        </a>
                    </li>
                @endif

            </ul>
        </nav>
    </div>

</aside>
