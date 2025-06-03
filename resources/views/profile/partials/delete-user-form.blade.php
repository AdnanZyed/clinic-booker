<x-adminlte-card title="{{ __('Delete Account') }}" theme="danger" icon="fas fa-trash">
    <form method="post" action="{{ route('profile.destroy') }}" class="form-horizontal">
        @csrf
        @method('delete')

        <p>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}</p>

        <x-adminlte-input name="password" type="password" label="{{ __('Password') }}" required/>

        <x-slot name="footerSlot">
            <x-adminlte-button type="submit" theme="danger" label="{{ __('Delete') }}"/>
        </x-slot>
    </form>
</x-adminlte-card>
