@php
    $data = $this->getData();
@endphp

<x-filament::widget>
    <x-filament::card>
        <div class="grid grid-cols-1 gap-4 text-center md:grid-cols-3">
            <div>
                <div class="text-lg font-semibold text-gray-700">Oral</div>
                <div class="text-2xl font-bold text-primary-600">{{ $data['oral_percent'] }}%</div>
                <div class="text-sm text-gray-500">{{ $data['is_oral'] }} dari {{ $data['total'] }}</div>
            </div>
            <div>
                <div class="text-lg font-semibold text-gray-700">Intravena (IV)</div>
                <div class="text-2xl font-bold text-primary-600">{{ $data['iv_percent'] }}%</div>
                <div class="text-sm text-gray-500">{{ $data['is_iv'] }} dari {{ $data['total'] }}</div>
            </div>
            <div>
                <div class="text-lg font-semibold text-gray-700">Intramuskular (IM)</div>
                <div class="text-2xl font-bold text-primary-600">{{ $data['im_percent'] }}%</div>
                <div class="text-sm text-gray-500">{{ $data['is_im'] }} dari {{ $data['total'] }}</div>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
