@if (session('status') === 'profile-updated')
    <x-adminlte-alert theme="success" title="{{ __('Updated successfuly') }}" dismissable />
@endif

<form method="post" action="{{ route('profile.update') }}" class="form-horizontal">
    @csrf
    @method('patch')

    <x-adminlte-card title="{{ __('Update Profile Information') }}" theme="lightblue" icon="fas fa-user">
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Ensure your account information is up to date.') }}
        </p>
        <x-adminlte-input name="name" label="{{ __('Name') }}" value="{{ old('name', auth()->user()->name) }}"
            required autofocus />
        <x-adminlte-input name="email" type="email" label="{{ __('Email') }}"
            value="{{ old('email', auth()->user()->email) }}" required />

        @if ($user->type === 'doctor' && $user->doctor)
            <x-adminlte-card title="{{ __('Doctor Information') }}" theme="teal" icon="fas fa-stethoscope">

                <x-adminlte-input name="specialization" label="{{ __('Specialization') }}"
                    value="{{ old('specialization', $user->doctor->specialization) }}" />

                <x-adminlte-input name="qualifications" label="{{ __('Qualifications') }}"
                    value="{{ old('qualifications', $user->doctor->qualifications) }}" />

                <x-adminlte-input name="session_duration" label="{{ __('Session Duration (minutes)') }}" type="number"
                    value="{{ old('session_duration', $user->doctor->session_duration) }}" />

                @php
                    $days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                    $selectedDays = json_decode($user->doctor->available_days ?? '[]');
                @endphp

                <x-adminlte-select name="available_days[]" label="{{ __('Available Days') }}" igroup-size="md" multiple>
                    @foreach($days as $day)
                        <option value="{{ $day }}" {{ in_array($day, $selectedDays) ? 'selected' : '' }}>
                            {{ __($day) }}
                        </option>
                    @endforeach
                </x-adminlte-select>

            </x-adminlte-card>
        @endif

        <x-slot name="footerSlot">
            <x-adminlte-button type="submit" theme="primary" label="{{ __('Save') }}" />
        </x-slot>
    </x-adminlte-card>
</form>
