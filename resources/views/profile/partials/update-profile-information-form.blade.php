@if (session('status') === 'profile-updated')
    <x-adminlte-alert theme="success" title="{{ __('Updated successfuly') }}" dismissable/>
@endif

<form method="post" action="{{ route('profile.update') }}" class="form-horizontal">
    @csrf
    @method('patch')

    <x-adminlte-card title="{{ __('Update Profile Information') }}" theme="lightblue" icon="fas fa-user">
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Ensure your account information is up to date.') }}
        </p>
        <x-adminlte-input name="name" label="{{ __('Name') }}" value="{{ old('name', auth()->user()->name) }}" required autofocus/>
        <x-adminlte-input name="email" type="email" label="{{ __('Email') }}" value="{{ old('email', auth()->user()->email) }}" required/>

        <x-slot name="footerSlot">
            <x-adminlte-button type="submit" theme="primary" label="{{ __('Save') }}"/>
        </x-slot>
    </x-adminlte-card>
</form>
