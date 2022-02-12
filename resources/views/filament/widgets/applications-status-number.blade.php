<x-filament::card class="relative">
    <div class="relative h-12 flex flex-col justify-center items-center space-y-2 rtl:space-x-reverse" style="color: {{$color}};">
    @php
        $route = route('filament.resources.applications.index', [
            'tableFilters' => [
                'status' => [
                    'values' => [$status]
                ],
            ],
        ]);
    @endphp
        <div class="space-y-1">
            <h2 class="text-lg sm:text-xl font-bold tracking-tight">
                <a href="{{ $route  }}">
                    {{ $count }}
                </a>
            </h2>
        </div>
        <div class="space-y-1 text-gray-600 focus:outline-none focus:underline">
            <a href="{{ $route  }}">
                {{ $status }} applications
            </a>
        </div>
    </div>
</x-filament::card>
