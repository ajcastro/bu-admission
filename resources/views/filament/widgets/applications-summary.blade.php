<x-filament::card class="relative">
@foreach ($items as $item)

    <div class="relative h-12 flex flex-col justify-center items-center space-y-2 rtl:space-x-reverse" style="color: {{$item->color}};">
    @php
        $route = route('filament.resources.applications.index', [
            'tableFilters' => [
                'status' => [
                    'values' => [$item->status]
                ],
            ],
        ]);
    @endphp
        <div class="space-y-1 text-gray-600 focus:outline-none focus:underline">
            <a href="{{ $route  }}">
                {{ $item->status }} applications
            </a>
        </div>
        <div class="space-y-1">
            <h2 class="text-lg sm:text-xl font-bold tracking-tight">
                <a href="{{ $route  }}">
                    {{ $item->count }}
                </a>
            </h2>
        </div>
    </div>
    <hr>
@endforeach
</x-filament::card>
