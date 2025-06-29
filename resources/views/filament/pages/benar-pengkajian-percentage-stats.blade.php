<x-filament-widgets::widget>
    <x-filament::card>

        {{-- Header Widget: Judul dan Filter Bulan --}}
        <div class="flex items-center justify-between gap-8">
            <h2 class="text-lg font-semibold tracking-tight sm:text-xl">
                Persentase Kepatuhan Benar Pasien
            </h2>

            {{-- Filter Bulan --}}
            <select wire:model.live="filter" wire:loading.attr="disabled"
                class="text-sm font-medium text-gray-900 border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                @foreach ($monthsOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Loading State --}}
        <div wire:loading.delay wire:target="filter" class="w-full text-center">
            <div class="py-12 text-sm text-gray-500">
                <x-filament::loading-indicator class="inline-block w-5 h-5" />
                <span>Memuat data...</span>
            </div>
        </div>

        {{-- Statistik Konten --}}
        <div wire:loading.remove.delay wire:target="filter">
            <div class="grid grid-cols-1 gap-6 mt-6 text-center md:grid-cols-3">

                {{-- Nama Pasien --}}
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800 w-48">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Suhu
                    </div>
                    <div class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-500">
                        {{ $suhu_percent }}%
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $is_suhu }} dari {{ $total }} data
                    </div>
                </div>

                {{-- Tanggal Lahir --}}
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800 w-48">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Tensi
                    </div>
                    <div class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-500">
                        {{ $tensi_percent }}%
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $is_tensi }} dari {{ $total }} data
                    </div>
                </div>

            </div>
        </div>

    </x-filament::card>
</x-filament-widgets::widget>
