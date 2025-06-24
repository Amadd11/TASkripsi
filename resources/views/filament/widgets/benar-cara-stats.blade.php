<x-filament-widgets::widget>
    <x-filament::card>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">
                {{ static::$heading }} {{-- Changed from $this->getHeading() to static::$heading --}}
            </h2>
            {{-- Dropdown Filter Bulan --}}
            <select wire:model.live="filter" {{-- Penting: wire:model.live untuk update real-time --}}
                class="block w-auto transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-primary-500 dark:focus:ring-primary-500">
                @foreach ($monthsOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Menampilkan Statistik (Mirip dengan StatsOverviewWidget) --}}
        <div class="grid gap-4 mb-4 md:grid-cols-2 lg:grid-cols-2">
            <div class="flex items-center p-4 bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="flex-shrink-0">
                    <x-heroicon-o-list-bullet class="w-8 h-8 text-info-500" />
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pencatatan</h3>
                    <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $total }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Jumlah data yang tercatat.</p>
                </div>
            </div>

            <div class="flex items-center p-4 bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="flex-shrink-0">
                    <x-heroicon-o-check-circle @class([
                        'w-8 h-8',
                        'text-success-500' => $allTruePct >= 90,
                        'text-warning-500' => $allTruePct >= 70 && $allTruePct < 90,
                        'text-danger-500' => $allTruePct < 70,
                    ]) />
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Benar Semua (Oral, IV, IM)</h3>
                    <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ number_format($allTruePct, 2) }}%
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $allTrue }} dari {{ $total }}
                        data</p>
                </div>
            </div>
        </div>

        {{-- Pesan jika tidak ada data --}}
        @if ($total === 0)
            <div class="mt-4 text-center text-gray-500 dark:text-gray-400">
                Tidak ada data ditemukan untuk bulan ini.
            </div>
        @endif
    </x-filament::card>
</x-filament-widgets::widget>
