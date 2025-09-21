<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}

        <x-filament::button type="submit">
            Simpan Pasca Tindakan
        </x-filament::button>
    </form>
</x-filament-panels::page>
