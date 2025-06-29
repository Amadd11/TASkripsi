<x-filament-widgets::widget>
    <x-filament::card>

        {{-- Header Widget --}}
        <div class="flex items-center justify-between gap-8">
            <h2 class="text-lg font-semibold tracking-tight sm:text-xl">
                Persentase Kepatuhan Benar Waktu
            </h2>

            {{-- Dropdown filter bulan --}}
            <select wire:model.live="filter" wire:loading.attr="disabled"
                class="text-sm font-medium text-gray-900 border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                @foreach ($monthsOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Loading indicator --}}
        <div wire:loading.delay wire:target="filter" class="w-full text-center">
            <div class="py-12 text-sm text-gray-500">
                <x-filament::loading-indicator class="inline-block w-5 h-5" />
                <span>Memuat data...</span>
            </div>
        </div>

        {{-- Statistik --}}
        <div wire:loading.remove.delay wire:target="filter">
            <div class="grid grid-cols-1 gap-6 mt-6 text-center md:grid-cols-3">

                {{-- Pagi --}}
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Pagi</div>
                    <div class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-500">
                        {{ $pagi_percent }}%
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $is_pagi }} dari {{ $total }} data
                    </div>
                </div>

                {{-- Siang --}}
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Siang</div>
                    <div class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-500">
                        {{ $siang_percent }}%
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $is_siang }} dari {{ $total }} data
                    </div>
                </div>

                {{-- Sore --}}
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Sore</div>
                    <div class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-500">
                        {{ $sore_percent }}%
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $is_sore }} dari {{ $total }} data
                    </div>
                </div>

                {{-- Malam --}}
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Malam</div>
                    <div class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-500">
                        {{ $malam_percent }}%
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $is_malam }} dari {{ $total }} data
                    </div>
                </div>

            </div>
        </div>

    </x-filament::card>
</x-filament-widgets::widget>
