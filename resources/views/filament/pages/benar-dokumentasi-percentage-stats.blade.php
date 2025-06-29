<x-filament-widgets::widget>
    <x-filament::card>
        {{-- Bagian Header dengan Judul dan Filter --}}
        <div class="flex items-center justify-between gap-8">
            <h2 class="text-lg font-semibold tracking-tight sm:text-xl">
                Persentase Kepatuhan 5 Benar
            </h2>

            {{-- Dropdown Filter Bulan (terhubung dengan Class Widget) --}}
            <select wire:model.live="filter" wire:loading.attr="disabled"
                class="text-sm font-medium text-gray-900 border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                @foreach ($monthsOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tampilan saat data sedang dimuat (muncul saat filter diganti) --}}
        <div wire:loading.delay class="w-full text-center">
            <div class="py-12 text-sm text-gray-500">
                <x-filament::loading-indicator class="inline-block w-5 h-5" />
                Memuat data...
            </div>
        </div>

        {{-- Konten Statistik (tampil saat tidak loading) --}}
        <div wire:loading.remove.delay>
            <div class="grid grid-cols-1 gap-6 mt-6 text-center md:grid-cols-3">

                {{-- Stat: Benar Pasien --}}
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Benar Pasien</div>
                    <div class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-500">
                        {{ $pasien_percent }}%</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $is_pasien }} dari {{ $total }}
                        data</div>
                </div>

                {{-- Stat: Benar Dosis --}}
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Benar Dosis</div>
                    <div class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-500">
                        {{ $dosis_percent }}%</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $is_dosis }} dari {{ $total }}
                        data</div>
                </div>

                {{-- Stat: Benar Obat --}}
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Benar Obat</div>
                    <div class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-500">{{ $obat_percent }}%
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $is_obat }} dari {{ $total }}
                        data</div>
                </div>

                {{-- Stat: Benar Waktu --}}
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Benar Waktu</div>
                    <div class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-500">
                        {{ $waktu_percent }}%</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $is_waktu }} dari {{ $total }}
                        data</div>
                </div>

                {{-- Stat: Benar Rute --}}
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Benar Rute</div>
                    <div class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-500">
                        {{ $rute_percent }}%</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $is_rute }} dari {{ $total }}
                        data</div>
                </div>
            </div>
        </div>

    </x-filament::card>
</x-filament-widgets::widget>
