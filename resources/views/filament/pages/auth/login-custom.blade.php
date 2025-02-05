
<x-filament-panels::page.simple>

    @if (filament()->hasRegistration())
        <x-slot name="subheading">
            {{ __('filament-panels::pages/auth/login.actions.register.before') }}

            {{ $this->registerAction }}
        </x-slot>
    @endif


    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}
        <x-filament::section
            collapsible
            collapsed
        icon="heroicon-m-exclamation-triangle"
        icon-color="danger">
            <x-slot name="heading">
                Testing Users
            </x-slot>

            <x-slot name="description">
                This website is currently under review. Please use fake details to create an account, or use the test account provided below to explore the features!
            </x-slot>

            admin@sajad.uk  <br>
            Password : 123456789
        </x-filament::section>
        

        <x-filament-panels::form id="form" wire:submit="authenticate">
        {{ $this->form }}


        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
</x-filament-panels::page.simple>
