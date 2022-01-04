<x-filament::card class="relative">
    <div class="relative h-12 flex flex-col justify-center items-center space-y-2 rtl:space-x-reverse">
        <div class="space-y-1">
            <h2 class="text-lg sm:text-xl font-bold tracking-tight">
                {{ App\Models\Term::where('is_active', 1)->firstOrNew()->label }}
            </h2>
        </div>
        <div class="space-y-1 text-gray-600 focus:outline-none focus:underline">
            Active Term
        </div>
    </div>
</x-filament::card>
