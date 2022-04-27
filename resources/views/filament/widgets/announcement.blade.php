<x-filament::card class="relative">
    <div class="relative h-12 flex flex-col items-center space-y-2 rtl:space-x-reverse">
        <div class="space-y-2">
            <h2 class="text-lg sm:text-xl font-bold tracking-tight">
                Announcement
            </h2>
        </div>
        <div class="space-y-2 text-gray-600 focus:outline-none focus:underline">
            {!! \App\Models\Setting::value('announcement') ?? '' !!}
        </div>
    </div>
</x-filament::card>
