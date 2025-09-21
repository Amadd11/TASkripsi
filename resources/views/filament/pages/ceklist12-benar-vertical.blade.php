<x-filament-panels::page>
    {{--
        1. Bungkus form dengan tag <form> dan hubungkan ke method `submit`
           menggunakan wire:submit.
    --}}
    <form wire:submit="submit">
        {{--
            2. Baris ini akan secara otomatis merender semua Section, Tabs,
               dan input yang sudah Anda definisikan di file PHP.
        --}}
        {{ $this->form }}

        {{--
            3. Tambahkan tombol "Simpan" di bawah form.
        --}}
        <div class="mt-6">
            <x-filament::button type="submit" size="lg">
                Simpan Checklist
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
