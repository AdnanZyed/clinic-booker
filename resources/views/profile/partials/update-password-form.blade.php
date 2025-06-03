@if (session('status') === 'password-updated')
    <x-adminlte-alert theme="success" title="{{ __('Updated Successfuly') }}" dismissable/>
@endif

<form method="post" action="{{ route('profile.updatePassword') }}" class="form-horizontal" id="update-password-form">
    @csrf
    @method('post')

    <x-adminlte-card title="{{ __('Update Password') }}" theme="warning" icon="fas fa-key">
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Ensure your account uses a long, random password to stay secure.') }}
        </p>
        <x-adminlte-input name="current_password" type="password" label="{{ __('Current Password') }}" required autocomplete="current-password"/>
        <x-adminlte-input id="password" name="password" type="password" label="{{ __('New Password') }}" required autocomplete="new-password"/>
        <x-adminlte-input id="password_confirmation" name="password_confirmation" type="password" label="{{ __('Confirm Password') }}" required autocomplete="new-password"/>
        <small id="password-error" class="text-danger d-none">{{ __('Passwords do not match') }}</small>

        <x-slot name="footerSlot">
            <x-adminlte-button type="submit" theme="primary" label="{{ __('Save') }}"/>
        </x-slot>
    </x-adminlte-card>
</form>

<script>
    $(document).ready(function () {
        $('#update-password-form').on('submit', function (e) {
            alert(33);
            e.preventDefault();
            const password = $('#password').val();
            const confirmPassword = $('#password_confirmation').val();

            if (password !== confirmPassword) {
                e.preventDefault();
                $('#password-error').removeClass('d-none');
            } else {
                $('#password-error').addClass('d-none');
            }
        });

        $('#password, #password_confirmation').on('input', function () {
            $('#password-error').addClass('d-none');
        });
    });
</script>