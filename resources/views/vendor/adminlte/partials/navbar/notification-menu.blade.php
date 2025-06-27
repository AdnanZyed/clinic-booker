@php
    $notifications = auth()->user()->notifications()->latest()->take(5)->get();
    $unreadCount = auth()->user()->unreadNotifications->count();
@endphp

<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fas fa-bell"></i>
        @if ($unreadCount)
            <span class="badge badge-warning navbar-badge">{{ $unreadCount }}</span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

        <span class="dropdown-header">{{ $unreadCount }} {{ __('New Notification') }}</span>
        <div class="dropdown-divider"></div>

        <div class="items" style="max-height: 400px; overflow-y: auto;">
            @foreach ($notifications as $notification)
                <a href="{{ route('notifications.read', $notification->id) }}"
                class="dropdown-item {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
                    <div class="media">
                        <div class="media-icon mr-3">
                            <i class="{{ $notification->data['icon'] }} fa-2x text-primary"></i>
                        </div>
                        <div class="media-body">
                            <h6 class="dropdown-item-title mb-1">
                                {{ $notification->data['title'] }}
                            </h6>
                            <p class="text-sm mb-0">
                                {{ $notification->data['body'] }}
                            </p>
                            <p class="text-sm text-muted">
                                <i class="far fa-clock mr-1"></i>
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider"></div>
            @endforeach
        </div>

        <a href="{{ route('notifications.readAll') }}" class="dropdown-item dropdown-footer text-center">
            {{ __('Mark all as read') }}
        </a>

        <a href="{{ route('notifications.index') }}" class="dropdown-item dropdown-footer text-center text-primary">
            {{ __('View All Notifications') }}
        </a>
    </div>
</li>
